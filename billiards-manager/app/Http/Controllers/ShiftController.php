<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        // 1. Stats
        $totalShifts = Shift::count();
        $activeShifts = Shift::where('status', 'active')->count();
        $inactiveShifts = Shift::where('status', 'inactive')->count();
        
        // Count unique employees currently checked in (today, no checkout)
        $currentWorkingEmployees = \App\Models\Attendance::whereDate('check_in', today())
            ->whereNull('check_out')
            ->distinct('employee_id')
            ->count('employee_id');

        // 2. Shifts List with "today's employee count"
        // We want to count how many employees are scheduled for this shift TODAY.
        $shifts = Shift::withCount(['employeeShifts' => function ($query) {
            $query->whereDate('shift_date', today());
        }])->orderBy('start_time', 'asc')->get();

        return view('admin.shifts.index', compact('shifts', 'totalShifts', 'activeShifts', 'inactiveShifts', 'currentWorkingEmployees'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i',
            'status'      => 'required|boolean',
        ], [
            'name.required'        => 'Vui lòng nhập tên ca làm việc.',
            'start_time.required'  => 'Vui lòng nhập thời gian bắt đầu.',
            'end_time.required'    => 'Vui lòng nhập thời gian kết thúc.',
            'status.required'      => 'Vui lòng chọn trạng thái ca làm việc.',
        ]);

        Shift::create($validated);

        return redirect()->route('admin.shifts.index')->with('success', 'Thêm ca làm việc thành công!');
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);

        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i',
            'status'      => 'required|in:active,inactive',
        ], [
            'name.required'        => 'Vui lòng nhập tên ca làm việc.',
            'start_time.required'  => 'Vui lòng nhập thời gian bắt đầu.',
            'end_time.required'    => 'Vui lòng nhập thời gian kết thúc.',
        ]);

        $shift = Shift::findOrFail($id);
        // var_dump($shift);die;
        $shift->update($validated);

        return redirect()->route('admin.shifts.index')->with('success', 'Cập nhật ca làm việc thành công!');
    }

    public function shiftEmployee(Request $request)
    {
        // Lấy ngày bắt đầu tuần (mặc định là thứ Hai của tuần hiện tại)
        $weekStart = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        // Kiểm tra xem tuần này đã qua chưa
        $isPastWeek = $weekStart->lt(Carbon::now()->startOfWeek());

        // Tạo danh sách 7 ngày trong tuần (Thứ 2 → Chủ Nhật)
        $weekDays = collect(range(0, 6))->map(function ($i) use ($weekStart, $isPastWeek) {
            $date = $weekStart->copy()->addDays($i);
            return [
                'day_name' => $this->getVietnameseDayName($date->dayOfWeek),
                'date' => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_past' => $date->lt(Carbon::now()->startOfDay()), // Ngày đã qua
                'is_today' => $date->isToday(),
            ];
        })->all();

        // Lấy nhân viên + ca làm việc trong tuần + shift
        $employees = Employee::with(['employeeShifts' => function ($query) use ($weekStart) {
            $query->whereBetween('shift_date', [
                $weekStart->format('Y-m-d'),
                $weekStart->copy()->addDays(6)->format('Y-m-d')
            ])->with('shift');
        }])->get();

        // Gắn class màu cho từng ca làm việc
        $employees->each(function ($employee) {
            $employee->employeeShifts->each(function ($employeeShift) {
                $shiftCode = $employeeShift->shift->code ?? 'default';
                $employeeShift->color_class = $this->getShiftColorClass($shiftCode);
            });
        });

        // Lấy toàn bộ ca làm việc (nếu cần dùng ở view)
        $shifts = Shift::all();

        return view('admin.shiftEmployee.index', compact(
            'employees',
            'weekDays',
            'weekStart',
            'shifts',
            'isPastWeek'
        ));
    }

    public function scheduleShifts(Request $request)
    {
        $request->validate([
            'shift_date' => 'required|date',
            'assignments' => 'required|array',
        ]);

        $date = $request->shift_date;
        $selectedDate = Carbon::parse($date);

        // Kiểm tra nếu ngày đã qua thì không cho phép sửa
        if ($selectedDate->lt(Carbon::now()->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể sửa ca làm việc của ngày đã qua: ' . $selectedDate->format('d/m/Y')
            ], 403);
        }

        // Nếu assignments rỗng (xóa ca), chỉ cần xóa ca cũ
        if (empty($request->assignments)) {
            EmployeeShift::whereDate('shift_date', $date)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ca làm việc cho ngày ' . $date
            ]);
        }

        // Validate assignments nếu có dữ liệu
        $request->validate([
            'assignments.*.employee_id' => 'required|exists:employees,id',
            'assignments.*.shift_id' => 'required|exists:shifts,id',
        ]);

        DB::transaction(function () use ($request, $date) {
            // Xóa các ca cũ của ngày đó cho employee cụ thể
            $employeeIds = collect($request->assignments)->pluck('employee_id')->toArray();
            EmployeeShift::whereDate('shift_date', $date)
                ->whereIn('employee_id', $employeeIds)
                ->delete();

            foreach ($request->assignments as $assignment) {
                EmployeeShift::create([
                    'employee_id' => $assignment['employee_id'],
                    'shift_id' => $assignment['shift_id'],
                    'shift_date' => $date,
                    'status' => EmployeeShift::STATUS_SCHEDULED,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Phân ca thành công cho ngày ' . $date
        ]);
    }

    public function saveWeeklySchedule(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
            'schedule' => 'required|array',
            'schedule.*.employee_id' => 'required|exists:employees,id',
            'schedule.*.date' => 'required|date',
            'schedule.*.shift_id' => 'nullable|exists:shifts,id',
        ]);

        $weekStart = Carbon::parse($request->week_start);

        // Kiểm tra nếu tuần đã qua thì không cho phép sửa
        if ($weekStart->lt(Carbon::now()->startOfWeek())) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể sửa lịch làm việc của tuần đã qua'
            ], 403);
        }

        $weekEnd = $weekStart->copy()->addDays(6);

        DB::transaction(function () use ($request, $weekStart, $weekEnd) {
            // Xóa tất cả ca trong tuần
            EmployeeShift::whereBetween('shift_date', [
                $weekStart->format('Y-m-d'),
                $weekEnd->format('Y-m-d')
            ])->delete();

            // Tạo ca mới
            foreach ($request->schedule as $assignment) {
                if (!empty($assignment['shift_id'])) {
                    // Kiểm tra từng ngày xem có phải ngày đã qua không
                    $assignmentDate = Carbon::parse($assignment['date']);
                    if ($assignmentDate->gte(Carbon::now()->startOfDay())) {
                        EmployeeShift::create([
                            'employee_id' => $assignment['employee_id'],
                            'shift_id' => $assignment['shift_id'],
                            'shift_date' => $assignment['date'],
                            'status' => EmployeeShift::STATUS_SCHEDULED,
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu lịch làm việc cho tuần từ ' . $weekStart->format('d/m/Y') . ' đến ' . $weekEnd->format('d/m/Y')
        ]);
    }

    public function bulkScheduleShifts(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.employee_id' => 'required|exists:employees,id',
            'assignments.*.shift_date' => 'required|date',
            'assignments.*.shift_id' => 'nullable|exists:shifts,id',
        ]);

        // Kiểm tra xem có ngày nào đã qua không
        foreach ($request->assignments as $assignment) {
            $assignmentDate = Carbon::parse($assignment['shift_date']);
            if ($assignmentDate->lt(Carbon::now()->startOfDay())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể sửa ca làm việc của ngày đã qua: ' . $assignmentDate->format('d/m/Y')
                ], 403);
            }
        }

        DB::transaction(function () use ($request) {
            foreach ($request->assignments as $assignment) {
                // Xóa ca cũ nếu tồn tại
                EmployeeShift::where('employee_id', $assignment['employee_id'])
                    ->whereDate('shift_date', $assignment['shift_date'])
                    ->delete();

                // Tạo ca mới nếu có shift_id
                if (!empty($assignment['shift_id'])) {
                    EmployeeShift::create([
                        'employee_id' => $assignment['employee_id'],
                        'shift_id' => $assignment['shift_id'],
                        'shift_date' => $assignment['shift_date'],
                        'status' => EmployeeShift::STATUS_SCHEDULED,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu thay đổi phân ca'
        ]);
    }

    // Thêm method mới: Copy ca từ tuần trước
    public function copyPreviousWeek(Request $request)
    {
        $request->validate([
            'target_week_start' => 'required|date',
        ]);

        $targetWeekStart = Carbon::parse($request->target_week_start);

        // Kiểm tra xem tuần mục tiêu có phải là tuần đã qua không
        if ($targetWeekStart->lt(Carbon::now()->startOfWeek())) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể sao chép ca vào tuần đã qua'
            ], 403);
        }

        $previousWeekStart = $targetWeekStart->copy()->subWeek();

        DB::transaction(function () use ($targetWeekStart, $previousWeekStart) {
            // Xóa tất cả ca trong tuần mục tiêu (chỉ những ngày chưa qua)
            EmployeeShift::whereBetween('shift_date', [
                $targetWeekStart->format('Y-m-d'),
                $targetWeekStart->copy()->addDays(6)->format('Y-m-d')
            ])->whereDate('shift_date', '>=', Carbon::now()->format('Y-m-d'))
                ->delete();

            // Lấy ca từ tuần trước
            $previousShifts = EmployeeShift::with('shift')
                ->whereBetween('shift_date', [
                    $previousWeekStart->format('Y-m-d'),
                    $previousWeekStart->copy()->addDays(6)->format('Y-m-d')
                ])->get();

            // Sao chép ca sang tuần mới (chỉ những ngày chưa qua)
            foreach ($previousShifts as $previousShift) {
                $newDate = $targetWeekStart->copy()->addDays(
                    Carbon::parse($previousShift->shift_date)->diffInDays($previousWeekStart)
                );

                // Chỉ sao chép nếu ngày đó chưa qua
                if ($newDate->gte(Carbon::now()->startOfDay())) {
                    EmployeeShift::create([
                        'employee_id' => $previousShift->employee_id,
                        'shift_id' => $previousShift->shift_id,
                        'shift_date' => $newDate->format('Y-m-d'),
                        'status' => EmployeeShift::STATUS_SCHEDULED,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã sao chép ca từ tuần trước sang tuần ' . $targetWeekStart->format('d/m/Y')
        ]);
    }

    // Helper methods
    private function getVietnameseDayName($dayOfWeek)
    {
        $days = [
            'Chủ nhật',
            'Thứ hai',
            'Thứ ba',
            'Thứ tư',
            'Thứ năm',
            'Thứ sáu',
            'Thứ bảy'
        ];

        return $days[$dayOfWeek] ?? 'Unknown';
    }

    private function getShiftColorClass($shiftCode)
    {
        $colors = [
            'MA' => 'bg-blue-100 text-blue-800 border-blue-200',
            'CH' => 'bg-orange-100 text-orange-800 border-orange-200',
            'TO' => 'bg-purple-100 text-purple-800 border-purple-200',
            'default' => 'bg-gray-100 text-gray-800 border-gray-200'
        ];

        return $colors[$shiftCode] ?? $colors['default'];
    }
}
