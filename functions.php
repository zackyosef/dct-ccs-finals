<?php
// All project functions should be placed here

function checkUserSessionIsActive() {
    if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
        header("Location: admin/dashboard.php");
        exit;
    }
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dct-ccs-finals');

// Function to connect to the database
function db_connection() {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check for connection errors
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    return $connection;
}

// Authenticate User and Initialize Session
function authenticate_and_login_user($email, $password) {
    $connection = db_connection();
    $password_hash = md5($password); // MD5 hash for password

    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $email, $password_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user data
        // Initialize session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        return true; // Login successful
    }

    return false; // Login failed
}


function render_alert($messages, $type = 'danger') {
    // Ensure messages is an array
    if (!is_array($messages)) {
        $messages = [$messages];
    }

    if (empty($messages)) {
        return '';
    }

    $html = '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">';
    $html .= '<ul>';
    foreach ($messages as $message) {
        $html .= '<li>' . htmlspecialchars($message) . '</li>';
    }
    $html .= '</ul>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';

    return $html;
}


// Function to fetch subject details
function fetch_subject_details($subject_id) {
    $connection = db_connection();
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to delete subject
function delete_subject($subject_id) {
    $connection = db_connection();
    $delete_query = "DELETE FROM subjects WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param('i', $subject_id);
    return $delete_stmt->execute(); 
}


?>
