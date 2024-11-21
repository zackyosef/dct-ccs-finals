<?php
ob_start(); 
require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';

guard(); // Ensure user is authenticated

// Initialize variables
$error_message = '';
$success_message = ''; // Define the variable to avoid "undefined variable" error

// Check if an ID is provided
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $student_data = manageStudentData($student_id);

    if (!$student_data) {
        $error_message = "Student not found.";
    }
} else {
    $error_message = "No student selected to edit.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    if (isset($student_id)) {
        $updated_data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
        ];

        // Validate updated data
        if (empty($updated_data['first_name']) || empty($updated_data['last_name'])) {
            $error_message = "First Name and Last Name are required.";
        } else {
            // Update the student record
            list($success_message, $error_message) = manageStudentData($student_id, $updated_data['first_name'], $updated_data['last_name']);
        }
    } else {
        $error_message = "Invalid student ID.";
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Student</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($student_data)): ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_data['student_id']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="<?php echo htmlspecialchars($student_data['first_name']); ?>">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($student_data['last_name']); ?>">
            </div>
            <div class="mb-3">
                <button type="submit" name="update_student" class="btn btn-primary w-100">Update Student</button>
            </div>
        </form>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); ?>
