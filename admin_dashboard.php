<?php
session_start();
include 'db_connect.php'; 

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
// Check for status messages after action
if (isset($_GET['action']) && isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $action = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
        $message = "<p style='color: green; font-weight: bold;'>✅ Project successfully {$action}.</p>";
    } elseif ($_GET['status'] == 'failed') {
        $message = "<p style='color: red; font-weight: bold;'>❌ Action failed. Database Error.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Pending Projects | Admin Panel</title>
</head>
<body>

<div class="admin-main">
    <?php include 'admin_nav.php'; // Navigation bar include karna ?>

    <div class="admin-content">
        <h1>Welcome Admin, <?php echo $_SESSION['name']; ?>!</h1>
        <h2>⚙️ Project Moderation Panel</h2>
        <?php echo $message; ?>

        <h3>Projects Needing Review (Status: Pending)</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Abstract</th>
                    <th>Tech Stack</th>
                    <th>Submitted By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Pending projects ko join karke user name ke saath display karna
                $sql_pending = "SELECT p.*, u.name AS senior_name 
                                FROM projects p
                                JOIN users u ON p.uploaded_by = u.id
                                WHERE p.status = 'Pending'
                                ORDER BY p.id ASC";
                $result_pending = $conn->query($sql_pending);

                if ($result_pending->num_rows > 0) {
                    while($row = $result_pending->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='project_detail.php?id=" . $row['id'] . "' style='color:#1e88e5; font-weight:bold; text-decoration:none;'>" . htmlspecialchars($row['title']) . "</a></td>";
                        echo "<td>" . substr(htmlspecialchars($row['abstract']), 0, 80) . "...</td>";
                        echo "<td>" . htmlspecialchars($row['tech_stack']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['senior_name']) . "</td>";
                        echo "<td>
                                <a href='admin_action.php?id=" . $row['id'] . "&action=approve' class='approve-btn'>Approve</a> 
                                <a href='admin_action.php?id=" . $row['id'] . "&action=reject' class='reject-btn'>Reject</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No pending projects to review.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
