# Hướng dẫn sử dụng Giả lập Máy chấm công QR

Hệ thống giả lập đã được xây dựng để bạn có thể test toàn bộ quy trình Check-in / Check-out trên localhost mà không cần thiết bị thật.

## 1. Truy cập Simulator
Mở trình duyệt và truy cập:
`http://localhost:8000/attendance/simulator`

## 2. Các tính năng chính

### A. Tạo Mã QR (Dành cho Nhân viên)
1. Ở cột bên trái **"1. Tạo Mã QR"**, chọn nhân viên bạn muốn test từ danh sách.
2. Nhấn nút **"Tạo Token Mới"**.
3. Hệ thống sẽ sinh ra một mã QR (hình ảnh) và một chuỗi Token (text).
   - **Mã QR**: Dùng để quét bằng Camera (nếu có webcam).
   - **Token Text**: Dùng để test thủ công (nếu không có webcam).
   - *Lưu ý*: Token chỉ có hiệu lực trong 2 phút.

### B. Giả lập Quét (Dành cho Máy chấm công)
Ở cột bên phải **"2. Giả lập Máy Quét"**, bạn có 2 chế độ:

#### Chế độ 1: Nhập Thủ Công (Không cần Camera)
Đây là cách test nhanh nhất trên localhost.
1. Copy chuỗi Token vừa tạo ở bước A.
2. Chọn tab **"Nhập Thủ Công"**.
3. Dán token vào ô input.
4. Nhấn **"Check"**.
5. Xem kết quả ở khung Log bên dưới.

#### Chế độ 2: Camera Laptop
Nếu laptop của bạn có webcam, bạn có thể test tính năng quét ảnh thật.
1. Chọn tab **"Camera Laptop"**.
2. Nhấn **"Bật Camera"**.
3. Đưa mã QR (trên điện thoại hoặc tab khác) vào trước camera.
4. Hệ thống sẽ tự động nhận diện và gửi request Check-in/Check-out.

## 3. Logic Check-in / Check-out
Hệ thống tự động phân biệt dựa trên trạng thái hiện tại của nhân viên:
- **Check-in**: Nếu nhân viên chưa có ca làm việc đang hoạt động (chưa check-in hôm nay hoặc đã check-out ca trước).
- **Check-out**: Nếu nhân viên đang có ca làm việc (đã check-in nhưng chưa check-out).

## 4. API & Backend
- **Route**: `/api/attendance/scan` (POST)
- **Controller**: `AttendanceController@processScan`
- **Logic**:
  1. Validate Token (phải tồn tại và chưa hết hạn).
  2. Tìm nhân viên tương ứng.
  3. Kiểm tra xem nhân viên có đang trong ca làm việc không (`EmployeeShift` active).
  4. Nếu có -> Thực hiện **Check-out**.
  5. Nếu không -> Tìm ca làm việc theo lịch (`Shift`) và thực hiện **Check-in**.

## 5. Thư viện sử dụng
- **Scanner**: `html5-qrcode` (Quét QR trên trình duyệt).
- **Generator**: `qrious` (Tạo QR trên trình duyệt).
