<?php
ob_start();
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();

// Initialize variables
$error_message = '';
$success_message = '';

if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);
    
    // Database connection
    $connection = db_connection();

    if ($connection && !$connection->connect_error) {
        // Fetching the student-subject record logic will be added later
    } else {
        $error_message = "Database connection failed.";
    }
} else {
    header("Location: attach-subject.php");
    exit;
}

if (isset($_GET['id'])) {
    $record = fetchStudentSubjectRecord($record_id, $connection);

    if ($record) {
        // Handle form submission for detaching subject
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detach_subject'])) {
            if (detachSubject($record_id, $connection)) {
                header("Location: attach-subject.php?id=" . htmlspecialchars($record['student_id']));
                exit;
            } else {
                $error_message = "Failed to detach the subject. Please try again.";
            }
        }
    } else {
        header("Location: attach-subject.php");
        exit;
    }
} else {
    header("Location: attach-subject.php");
    exit;
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Detach a Subject</h1>

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
</main>

