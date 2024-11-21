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
?>
