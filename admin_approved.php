<?php
session_start();
include 'db_connect.php'; 

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Approved Projects | Admin Panel</title>
</head>
<body>

<div class="admin-main">
    <?php include 'admin_nav.php'; // Navigation bar include karna ?>

    <div class="admin-content"> 
        <h1>✅ Approved Projects List</h1>
        <p>These projects are currently live on the public website.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Tech Stack</th>
                    <th>Submitted By</th>
                    <th>Year</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sirf Approved projects ko display karna
                $sql_approved = "SELECT p.title, p.tech_stack, p.year, u.name AS senior_name 
                                FROM projects p
                                JOIN users u ON p.uploaded_by = u.id
                                WHERE p.status = 'Approved'
                                ORDER BY p.id DESC";
                $result_approved = $conn->query($sql_approved);

                if ($result_approved->num_rows > 0) {
                    while($row = $result_approved->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tech_stack']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['senior_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No projects have been approved yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
