<?php 
/**
 * Staff Registration Page
 * Handles the logic for creating a new staff account.
 */

require_once '../db/db_conn.php';
require_once '../includes/header.php'; 

$message = "";
$status = "";

// Handle Registration Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $user_id    = trim($_POST['user_id'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';

    // Validation Rules
    if (!preg_match('/^U\d{3}$/', $user_id)) {
        $message = "Invalid User ID format. Use U001 (e.g., U001, U002).";
        $status = "danger";
    } elseif (strlen($password) <= 8) {
        $message = "Password must be more than 8 characters long for security.";
        $status = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $status = "danger";
    } else {
        // Check if username or email already exists
        $check_stmt = $conn->prepare("SELECT user_id FROM user WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "The username or email address is already registered.";
            $status = "danger";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (user_id, first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $user_id, $first_name, $last_name, $email, $username, $hashed_password);

            if ($stmt->execute()) {
                // Success redirect
                header("Location: login.php?status=registered");
                exit();
            } else {
                $message = "An error occurred during registration: " . $conn->error;
                $status = "danger";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4 border-0">
                <div class="card-body">
                    <h3 class="text-center mb-4">Staff Registration</h3>
                    
                    <!-- Feedback Messages -->
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="row">
                            <!-- User ID -->
                            <div class="col-md-12 mb-3">
                                <label for="user_id" class="form-label">User ID (Format: U001)</label>
                                <input type="text" class="form-control" id="user_id" name="user_id" 
                                       placeholder="e.g., U001"
                                       value="<?php echo htmlspecialchars($_POST['user_id'] ?? ''); ?>" required>
                            </div>

                            <!-- First & Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>

                            <!-- Username -->
                            <div class="col-md-12 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>

                            <!-- Password -->
                            <div class="col-md-12 mb-3">
                                <label for="password" class="form-label">Password (Min 9 characters)</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text text-muted">Use a strong password to protect your account.</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-person-check"></i> Register Staff
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
