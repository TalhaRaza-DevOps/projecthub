<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if project ID and action are provided in the URL
if (isset($_GET['id']) && isset($_GET['action'])) {
    $project_id = (int)$_GET['id'];
    $action = $_GET['action'];

    $new_status = '';

    if ($action == 'approve') {
        $new_status = 'Approved';
    } elseif ($action == 'reject') {
        $new_status = 'Rejected';
    } else {
        // Agar action invalid ho toh wapis bhej do
        header("Location: admin_dashboard.php");
        exit();
    }

    // SQL UPDATE Query
    $sql = "UPDATE projects SET status = '$new_status' WHERE id = $project_id";

    if ($conn->query($sql) === TRUE) {
        // Success hone par dashboard par wapis bhej do status message ke saath
        header("Location: admin_dashboard.php?action=$action&status=success");
        exit();
    } else {
        // Error hone par wapis bhej do
        header("Location: admin_dashboard.php?action=$action&status=failed");
        exit();
    }
}

$conn->close();
// Agar ID ya action set nahi hai, toh wapis dashboard par bhej do
header("Location: admin_dashboard.php");
exit();
?>
