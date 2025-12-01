<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm công - Billiards Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="flex flex-col md:flex-row gap-8 w-full max-w-4xl">
        <!-- Check-in Form -->
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full md:w-1/2">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Chấm Công</h1>
                <p class="text-gray-500 mt-2">Nhập mã nhân viên để Check-in/Check-out</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employeeCode">
                    Mã Nhân Viên
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input class="shadow appearance-none border rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition" 
                        id="employeeCode" type="text" placeholder="Ví dụ: NV001">
                </div>
            </div>

            <div class="flex space-x-4">
                <button onclick="checkIn()" class="w-1/2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Check In
                </button>
                <button onclick="checkOut()" class="w-1/2 bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                </button>
            </div>

            <div id="clock" class="text-center mt-8 text-gray-400 font-mono text-xl">
                --:--:--
            </div>
        </div>

        <!-- Active Employees List -->
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full md:w-1/2">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Đang trong ca làm việc</h2>
            <div id="activeEmployeesList" class="space-y-3 max-h-[400px] overflow-y-auto">
                <!-- List items will be injected here -->
                <div class="text-center text-gray-400 py-4">Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- Manager Approval Modal -->
    <div id="managerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-96">
            <h3 class="text-lg font-bold mb-4 text-red-600">Đi muộn - Cần Quản lý duyệt</h3>
            <p class="text-sm text-gray-600 mb-4">Nhân viên đã đi muộn quá 15 phút. Vui lòng nhập thông tin quản lý để xác nhận.</p>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Quản lý</label>
                <input type="email" id="managerEmail" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu</label>
                <input type="password" id="managerPassword" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button onclick="closeManagerModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Hủy</button>
                <button onclick="submitManagerApproval()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Xác nhận</button>
            </div>
        </div>
    </div>

    <script>
        let serverTimeOffset = 0;

        function fetchServerTime() {
            fetch('/api/attendance/server-time')
                .then(response => response.json())
                .then(data => {
                    const serverTime = new Date(data.time).getTime();
                    const localTime = new Date().getTime();
                    serverTimeOffset = serverTime - localTime;
                    updateClock();
                })
                .catch(err => console.error('Failed to fetch server time:', err));
        }

        function updateClock() {
            const now = new Date(new Date().getTime() + serverTimeOffset);
            document.getElementById('clock').innerText = now.toLocaleTimeString('vi-VN');
        }

        // Fetch server time immediately and then every minute to stay synced
        fetchServerTime();
        setInterval(fetchServerTime, 60000);
        setInterval(updateClock, 1000);

        function fetchActiveEmployees() {
            fetch('/api/attendance/active')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('activeEmployeesList');
                    list.innerHTML = '';
                    if (data.data.length === 0) {
                        list.innerHTML = '<div class="text-center text-gray-400 py-4">Chưa có nhân viên nào check-in</div>';
                        return;
                    }
                    data.data.forEach(emp => {
                        const item = `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                        ${emp.name.charAt(0)}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800">${emp.name}</div>
                                        <div class="text-xs text-gray-500 capitalize">${emp.position}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-400">Vào ca lúc</div>
                                    <div class="font-bold text-green-600">${emp.start_time}</div>
                                </div>
                            </div>
                        `;
                        list.innerHTML += item;
                    });
                })
                .catch(err => console.error(err));
        }

        // Fetch immediately and then every 30 seconds
        fetchActiveEmployees();
        setInterval(fetchActiveEmployees, 30000);

        function checkIn() {
            const code = document.getElementById('employeeCode').value;
            if (!code) {
                Swal.fire('Lỗi', 'Vui lòng nhập mã nhân viên', 'warning');
                return;
            }
            performAction('/api/attendance/check-in', code, 'Check-in');
        }

        function checkOut() {
            const code = document.getElementById('employeeCode').value;
            if (!code) {
                Swal.fire('Lỗi', 'Vui lòng nhập mã nhân viên', 'warning');
                return;
            }
            performAction('/api/attendance/check-out', code, 'Check-out');
        }

        let currentEmployeeCode = '';

        function performAction(url, code, actionName, managerData = null) {
            currentEmployeeCode = code;
            
            Swal.fire({
                title: 'Đang xử lý...',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const body = { employee_code: code };
            if (managerData) {
                body.manager_username = managerData.username;
                body.manager_password = managerData.password;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    Swal.fire('Thành công', body.message, 'success');
                    document.getElementById('employeeCode').value = ''; // Clear input
                    closeManagerModal();
                    fetchActiveEmployees(); // Refresh list
                } else if (status === 403 && body.status === 'REQUIRE_MANAGER_APPROVAL') {
                    Swal.close();
                    showManagerModal();
                } else {
                    let errorMessage = body.message;
                    if (body.debug) {
                        errorMessage += `<br><br><div class="text-left text-xs bg-gray-100 p-2 rounded">
                            <strong>Server Time:</strong> ${body.debug.server_time}<br>
                            <strong>Timezone:</strong> ${body.debug.timezone}<br>
                            ${body.debug.shifts_found ? '<strong>Shifts Found:</strong> ' + JSON.stringify(body.debug.shifts_found) : ''}
                        </div>`;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        html: errorMessage,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi', 'Có lỗi xảy ra khi kết nối server', 'error');
            });
        }

        function showManagerModal() {
            document.getElementById('managerModal').classList.remove('hidden');
            document.getElementById('managerModal').classList.add('flex');
        }

        function closeManagerModal() {
            document.getElementById('managerModal').classList.add('hidden');
            document.getElementById('managerModal').classList.remove('flex');
            document.getElementById('managerEmail').value = '';
            document.getElementById('managerPassword').value = '';
        }

        function submitManagerApproval() {
            const email = document.getElementById('managerEmail').value;
            const password = document.getElementById('managerPassword').value;

            if (!email || !password) {
                alert('Vui lòng nhập đầy đủ thông tin quản lý');
                return;
            }

            performAction('/api/attendance/check-in', currentEmployeeCode, 'Check-in', {
                username: email,
                password: password
            });
        }
    </script>
</body>
</html>
