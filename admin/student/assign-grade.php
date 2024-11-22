<?php
ob_start(); 
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard(); 

$error_message = '';
$success_message = '';

// Get the record ID from either POST or GET
$record_id = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

?>
