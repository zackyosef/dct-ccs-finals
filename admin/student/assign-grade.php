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
            // Logic for assigning or updating the grade will be added later
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
