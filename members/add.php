<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!preg_match('/^M\d{3}$/', $member_id)) {
        $message = "Invalid Member ID format (e.g. M001)";
        $status = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $status = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO member (member_id, first_name, last_name, birthday, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $member_id, $first_name, $last_name, $birthday, $email);
        if ($stmt->execute()) { header("Location: index.php"); exit(); }
        else { $message = "Error: " . $conn->error; $status = "danger"; }
        $stmt->close();
    }
}
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Add Member</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Member ID (M001)</label><input type="text" class="form-control" name="member_id" required></div>
        <div class="mb-3"><label class="form-label">First Name</label><input type="text" class="form-control" name="first_name" required></div>
        <div class="mb-3"><label class="form-label">Last Name</label><input type="text" class="form-control" name="last_name" required></div>
        <div class="mb-3"><label class="form-label">Birthday</label><input type="date" class="form-control" name="birthday" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
