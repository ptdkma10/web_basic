<?php
// dashboard.php
session_start();
include 'db_connect.php';

// 1. Kiểm tra xem user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa, chuyển về trang login
    header("Location: login.php");
    exit();
}

// 2. Lấy thông tin của người dùng đang đăng nhập
$user_id = $_SESSION['user_id'];
$user_info = null;

$sql_user = "SELECT ho_ten, gioi_tinh, email, dia_chi, so_dien_thoai FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_info = $result_user->fetch_assoc();
}
$stmt_user->close();


// 3. Xử lý chức năng tìm kiếm
$search_results = [];
$search_query = '';

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    
    // Tìm kiếm theo tên HOẶC email
    $sql_search = "SELECT id, ho_ten, email, so_dien_thoai FROM users WHERE ho_ten LIKE ? OR email LIKE ?";
    $search_term = "%" . $search_query . "%"; // Thêm ký tự wildcard
    
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("ss", $search_term, $search_term);
    $stmt_search->execute();
    $result_search = $stmt_search->get_result();
    
    while($row = $result_search->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt_search->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang cá nhân</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container" style="max-width: 800px;">
        <h2>Chào mừng, <?php echo htmlspecialchars($_SESSION['user_ho_ten']); ?>!</h2>
        <a href="logout.php">Đăng xuất</a>

        <hr>

        <h3>Thông tin cá nhân của bạn</h3>
        <?php if ($user_info): ?>
        <div class="user-info">
            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($user_info['ho_ten']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
            <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($user_info['gioi_tinh']); ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($user_info['dia_chi']); ?></p>
            <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($user_info['so_dien_thoai']); ?></p>
        </div>
        <?php else: ?>
        <p>Không tìm thấy thông tin người dùng.</p>
        <?php endif; ?>

        <hr>

        <h3>Tìm kiếm Người dùng khác</h3>
        <form action="dashboard.php" method="GET">
            <input type="text" name="search" placeholder="Nhập tên hoặc email để tìm"
                value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <?php if (isset($_GET['search'])): ?>
        <div class="search-results">
            <h4>Kết quả tìm kiếm cho "<?php echo htmlspecialchars($search_query); ?>"</h4>
            <?php if (count($search_results) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                </tr>
                <?php foreach ($search_results as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['ho_ten']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['so_dien_thoai']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <p>Không tìm thấy người dùng nào phù hợp.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</body>

</html>