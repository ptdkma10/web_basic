<?php
// login.php
session_start(); // Bắt đầu session
include 'db_connect.php';
$message = '';

// Nếu đã đăng nhập, chuyển hướng đến dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mat_khau_nhap = $_POST['mat_khau'];

    // 1. Lấy thông tin user từ CSDL
    $sql = "SELECT id, email, mat_khau, ho_ten FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // 2. So sánh mật khẩu đã hash
        if (password_verify($mat_khau_nhap, $user['mat_khau'])) {
            // Đăng nhập thành công
            // Lưu thông tin vào SESSION
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_ho_ten'] = $user['ho_ten'];
            
            // Chuyển hướng đến trang dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Sai email hoặc mật khẩu.";
        }
    } else {
        $message = "Sai email hoặc mật khẩu.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Đăng Nhập</h2>

        <?php if (!empty($message)): ?>
        <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            Email:
            <input type="email" name="email" required>

            Mật khẩu:
            <input type="password" name="mat_khau" required>

            <button type="submit">Đăng Nhập</button>
        </form>
        <p style="text-align:center;">Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a></p>
    </div>
</body>

</html>