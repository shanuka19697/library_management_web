<?php 
/**
 * Add New Book Page
 * Handles the logic for adding a new book to the database.
 */

require_once '../db/db_conn.php';
require_once '../includes/header.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = ""; 
$status = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = trim($_POST['book_id'] ?? '');
    $book_name = trim($_POST['book_name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';

    // Validate Book ID format (e.g., B001)
    if (!preg_match('/^B\d{3}$/', $book_id)) {
        $message = "Invalid Book ID format (e.g., B001)";
        $status = "danger";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO book (book_id, book_name, category_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $book_id, $book_name, $category_id);
        
        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $status = "danger";
        }
        $stmt->close();
    }
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM bookcategory");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h3 class="mb-4">Add New Book</h3>

                <!-- Alert Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Book ID -->
                    <div class="mb-3">
                        <label for="book_id" class="form-label">Book ID (Format: B001)</label>
                        <input type="text" class="form-control" id="book_id" name="book_id" 
                               placeholder="e.g., B001" required>
                    </div>

                    <!-- Book Name -->
                    <div class="mb-3">
                        <label for="book_name" class="form-label">Book Name</label>
                        <input type="text" class="form-control" id="book_name" name="book_name" 
                               placeholder="Enter book title" required>
                    </div>

                    <!-- Category Selection -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                                    <?php echo htmlspecialchars($cat['category_Name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Save Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
