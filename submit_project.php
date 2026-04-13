<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in and is a Senior
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'senior') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_project'])) {
    // --- 1. Form Data Fetching ---
    $title = $conn->real_escape_string($_POST['title']);
    $abstract = $conn->real_escape_string($_POST['abstract']);
    $tech_stack = $conn->real_escape_string($_POST['tech_stack']);
    $year = (int)$_POST['year'];
    $github_link = $conn->real_escape_string($_POST['github_link']);
    $uploaded_by = $_SESSION['user_id'];
    $upload_dir = 'uploads/project_images/';
    $screenshot_paths = [];
    $max_files = 5;

    // --- 2. Multiple File Upload Handling ---
    if (isset($_FILES['screenshots'])) {
        $files = $_FILES['screenshots'];
        $total_files = count($files['name']);
        
        if ($total_files > $max_files || $total_files < 1) {
             $_SESSION['error_message'] = "You must upload between 1 and {$max_files} screenshots.";
             header("Location: senior_dashboard.php?submission=failed");
             exit();
        }

        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
            $file_type = $files['type'][$i];
            
            // Check for upload errors
            if ($file_error !== 0) {
                $_SESSION['error_message'] = "Error uploading file: {$file_name}.";
                header("Location: senior_dashboard.php?submission=failed");
                exit();
            }

            // Check file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error_message'] = "Only JPG, JPEG, and PNG images are allowed.";
                header("Location: senior_dashboard.php?submission=failed");
                exit();
            }

            // Generate unique file name
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'proj_' . $uploaded_by . '_' . time() . '_' . $i . '.' . $file_extension;
            $target_file = $upload_dir . $new_file_name;

            // Move file
            if (move_uploaded_file($file_tmp, $target_file)) {
                $screenshot_paths[] = $target_file;
            } else {
                $_SESSION['error_message'] = "File move failed for {$file_name}. Check folder permissions.";
                header("Location: senior_dashboard.php?submission=failed");
                exit();
            }
        }
    } else {
         $_SESSION['error_message'] = "Screenshots are required for submission.";
         header("Location: senior_dashboard.php?submission=failed");
         exit();
    }

    // --- 3. Database Insertion ---
    // Paths ko comma-separated string mein join karna
    $screenshots_db = implode(',', $screenshot_paths);

    $sql = "INSERT INTO projects (title, abstract, tech_stack, year, github_link, uploaded_by, status, screenshots) 
            VALUES ('$title', '$abstract', '$tech_stack', $year, '$github_link', $uploaded_by, 'Pending', '$screenshots_db')";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        // Success hone par submissions list par redirect karna
        header("Location: senior_dashboard.php?submission=success");
        exit();
    } else {
        $_SESSION['error_message'] = "Database Error: " . $conn->error;
        $conn->close();
        header("Location: senior_dashboard.php?submission=failed");
        exit();
    }
}

// Default redirect agar direct access ho
header("Location: senior_dashboard.php");
exit();
?>