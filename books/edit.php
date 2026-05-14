<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$id = $_GET['id'] ?? '';
$message = ""; $status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name = $_POST['book_name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    $stmt = $conn->prepare("UPDATE book SET book_name = ?, category_id = ? WHERE book_id = ?");
    $stmt->bind_param("sss", $book_name, $category_id, $id);
    if ($stmt->execute()) { $message = "Updated!"; $status = "success"; }
    else { $message = "Error: " . $conn->error; $status = "danger"; }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM book WHERE book_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

$categories = $conn->query("SELECT * FROM bookcategory");
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Edit Book</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Book ID</label><input type="text" class="form-control" value="<?php echo $book['book_id']; ?>" readonly></div>
        <div class="mb-3"><label class="form-label">Book Name</label><input type="text" class="form-control" name="book_name" value="<?php echo $book['book_name']; ?>" required></div>
        <div class="mb-3"><label class="form-label">Category</label>
            <select class="form-select" name="category_id" required>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $book['category_id']) ? 'selected' : ''; ?>><?php echo $cat['category_Name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
