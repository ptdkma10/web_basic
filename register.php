<?php
// register.php
include 'db_connect.php'; // Kết nối CSDL
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $dia_chi = $_POST['dia_chi'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    // 1. Kiểm tra Email đã tồn tại chưa
    $sql_check = "SELECT id FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $message = "Email đã tồn tại. Vui lòng sử dụng email khác.";
    } else {
        // 2. Mã hóa mật khẩu
        // **QUAN TRỌNG:** Luôn mã hóa mật khẩu trước khi lưu
        $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);

        // 3. Thêm người dùng vào CSDL (Sử dụng Prepared Statements để chống SQL Injection)
        $sql_insert = "INSERT INTO users (ho_ten, email, mat_khau, gioi_tinh, dia_chi, so_dien_thoai) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $ho_ten, $email, $hashed_password, $gioi_tinh, $dia_chi, $so_dien_thoai);

        if ($stmt_insert->execute()) {
            $message = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } else {
            $message = "Lỗi: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Đăng Ký Tài Khoản</h2>

        <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'thành công') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            Họ tên:
            <input type="text" name="ho_ten" required>

            Email:
            <input type="email" name="email" required>

            Mật khẩu:
            <input type="password" name="mat_khau" required>

            Giới tính:
            <input type="radio" name="gioi_tinh" value="Nam" required> Nam
            <input type="radio" name="gioi_tinh" value="Nữ"> Nữ
            <input type="radio" name="gioi_tinh" value="Khác"> Khác
            <br><br>

            Địa chỉ:
            <textarea name="dia_chi"></textarea>

            Số điện thoại:
            <input type="tel" name="so_dien_thoai">

            <button type="submit">Đăng Ký</button>
        </form>
        <p style="text-align:center;">Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a></p>
    </div>
</body>

</html>