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
    <title>Student Verification | Admin Panel</title>
</head>
<body>

<div class="admin-main">
    <?php include 'admin_nav.php'; // Navigation bar include karna ?>

    <div class="admin-content"> 
        <h1>🧑‍🎓 Student Verification List</h1>
        <p>Review student details and their submitted ID pictures to confirm university affiliation.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Name & ID</th>
                    <th>Program/Semester</th>
                    <th>Department</th>
                    <th>Student Card Picture</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Admin aur senior user roles ko display karna (admin ko skip karna)
                $sql_users = "SELECT uni_id, name, program, semester, department, card_pic FROM users WHERE role != 'admin' ORDER BY id DESC";
                $result_users = $conn->query($sql_users);

                if ($result_users->num_rows > 0) {
                    while($row = $result_users->fetch_assoc()) {
                        $pic_path = htmlspecialchars($row['card_pic']);
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . " <br><small>(" . htmlspecialchars($row['uni_id']) . ")</small></td>";
                        echo "<td>" . htmlspecialchars($row['program']) . " - Sem " . htmlspecialchars($row['semester']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                        
                        // Picture display logic
                        echo "<td>";
                        if (!empty($pic_path) && file_exists($pic_path)) {
                            echo "<a href='{$pic_path}' target='_blank'><img src='{$pic_path}' alt='Student Card' class='img-preview'></a>";
                        } else {
                            echo "Image Not Found";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No students registered yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
