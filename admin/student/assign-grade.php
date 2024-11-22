<?php
ob_start(); 
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();


$error_message = '';
$success_message = '';

// Get the record ID from either POST or GET
$record_id = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

if ($record_id) {
    // Fetch student and subject data based on the record ID
    $connection = db_connection();

    if ($connection && !$connection->connect_error) {
        $record = fetchRecord($record_id, $connection);

        if ($record) {
            // Handle form submission for assigning or updating the grade
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
                $grade = $_POST['grade'];

                // Validate grade
                $error_message = validateGrade($grade);
                if (!$error_message) {
                    $grade = floatval($grade);

                    if (updateGrade($record_id, $grade, $connection)) {
                        // Redirect to attach page after successful grade assignment with correct student ID
                        $success_message = "Grade successfully assigned.";
                        header("Location: attach-subject.php?id=" . htmlspecialchars($record['student_id']));
                        exit;
                    } else {
                        $error_message = "Failed to assign the grade. Please try again.";
                    }
                }
            }
        } else {
            header("Location: attach-subject.php");
            exit;
        }
    } else {
        $error_message = "Database connection failed.";
    }
} else {
    header("Location: attach-subject.php");
    exit;
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Assign Grade to Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id'] ?? ''); ?>">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
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

    <?php if (isset($record)): ?>
        <div class="card">
            <div class="card-body">
                <h5>Selected Student and Subject Information</h5>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($record['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($record['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($record['subject_name']); ?></li>
                </ul>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($record_id); ?>">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="number" step="0.01" class="form-control" id="grade" name="grade" value="<?php echo htmlspecialchars($record['grade']); ?>">
                    </div>
                    <a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade to Subject</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush();?>