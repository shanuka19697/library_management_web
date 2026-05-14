<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $category_name = $_POST['category_name'] ?? '';
    $current_date = date('Y-m-d H:i:s');

    if (!preg_match('/^C\d{3}$/', $category_id)) {
        $message = "Invalid Category ID format (e.g. C001)";
        $status = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookcategory (category_id, category_Name, date_modified) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $category_id, $category_name, $current_date);
        if ($stmt->execute()) { header("Location: index.php"); exit(); }
        else { $message = "Error: " . $conn->error; $status = "danger"; }
        $stmt->close();
    }
}
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Add Category</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Category ID (C001)</label><input type="text" class="form-control" name="category_id" required></div>
        <div class="mb-3"><label class="form-label">Category Name</label><input type="text" class="form-control" name="category_name" required></div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
