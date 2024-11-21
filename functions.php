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

// Function to check if the user is logged in and redirect if not
function guard() {
    // Check if the user is logged in
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        // Get the base URL dynamically
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $baseURL = $protocol . $host . '/'; // Ensure it points to the root

        // Redirect to the base URL
        header("Location: " . $baseURL);
        exit();
    }
}

// Function to get selected student data and update student record
function manageStudentData($student_id, $first_name = null, $last_name = null) {
    global $success_message, $error_message; // Declare the variables globally
    
    $connection = db_connection();
    
    if ($first_name !== null && $last_name !== null) {
        // Update student record
        $query = "UPDATE students SET first_name = ?, last_name = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('ssi', $first_name, $last_name, $student_id);
        
        if ($stmt->execute()) {
            $success_message = "Student record successfully updated.";
            header("Location: ../student/register.php"); // Redirect to the register page after update
            exit();
        } else {
            $error_message = "Failed to update student record. Error: " . $stmt->error;
        }
        
        $stmt->close();
        $connection->close();
    } else {
        // Fetch student data
        $query = "SELECT * FROM students WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
        return $student;
    }
}


function getSelectedStudentData($student_id) {
    $connection = db_connection();
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
    
    $stmt->close();
    $connection->close();

    return $student_data;
}

function deleteStudent($student_id) {
    $connection = db_connection();
    $query = "DELETE FROM students WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);

    if ($stmt->execute()) {
        $result = ['success' => true, 'message' => ''];
    } else {
        $result = ['success' => false, 'message' => "Failed to delete student record. Error: " . $stmt->error];
    }

    $stmt->close();
    $connection->close();

    return $result;
}
function getAvailableSubjects($student_id, $connection) {
    $query = "SELECT * FROM subjects WHERE id NOT IN (SELECT subject_id FROM students_subjects WHERE student_id = ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    return $stmt->get_result();
}

function handleAttachSubjects($student_id, $subjects, $connection) {
    if (!empty($subjects)) {
        foreach ($subjects as $subject_id) {
            $query = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($query);
            if ($stmt) {
                $grade = 0.00; // Default grade value
                $stmt->bind_param('iid', $student_id, $subject_id, $grade);
                $stmt->execute();
            }
        }
    } else {
        global $error_message;
        $error_message = "Please select at least one subject to attach.";
    }
}

function getAttachedSubjects($student_id, $connection) {
    $query = "SELECT subjects.subject_code, subjects.subject_name, students_subjects.grade, students_subjects.id 
              FROM subjects 
              JOIN students_subjects ON subjects.id = students_subjects.subject_id 
              WHERE students_subjects.student_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
