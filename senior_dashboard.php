<?php
session_start();
include 'db_connect.php'; 

// Check if user is logged in and is a Senior
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'senior') {
    header("Location: login.php");
    exit();
}

$view = isset($_GET['view']) ? $_GET['view'] : 'submit'; // Default view: submit form

$message = '';
// URL parameters se success/fail message check karna
if (isset($_GET['submission'])) {
    if ($_GET['submission'] == 'success') {
        $message = '<p style="color: green; font-weight: bold;">✅ Project submitted successfully! Waiting for Admin Approval.</p>';
        $view = 'list'; // Success ke baad list par redirect kar den
    } elseif ($_GET['submission'] == 'failed' && isset($_SESSION['error_message'])) {
        $message = '<p style="color: red; font-weight: bold;">❌ Submission failed: ' . $_SESSION['error_message'] . '</p>';
        // Error dikhane ke baad session variable ko clear karna
        $message .= '<p style="color: red;">Please try again.</p>';
        unset($_SESSION['error_message']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Senior Dashboard | Project Nexus</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<div class="senior-main">
    <?php include 'senior_nav.php'; // Navigation bar include karna ?>

    <div class="senior-content">
        <h1>Welcome Senior, <?php echo $_SESSION['name']; ?>!</h1>
        <?php echo $message; ?>

        <?php if ($view == 'submit'): ?>
        
            <h2>📥 Submit Your Final Year Project</h2>
            <form action="submit_project.php" method="POST" enctype="multipart/form-data"> 
                <label for="title">Project Title:</label><br>
                <input type="text" id="title" name="title" required><br>

                <label for="abstract">Abstract/Summary (What it does):</label><br>
                <textarea id="abstract" name="abstract" rows="4" required></textarea><br>

                <label for="tech_stack">Technologies Used (e.g., PHP, MySQL, CSS):</label><br>
                <input type="text" id="tech_stack" name="tech_stack" required><br>

                <label for="year">Year of Submission (e.g., 2025):</label><br>
                <input type="text" id="year" name="year" required><br>
                
                <label for="github_link">GitHub Link (Optional but recommended):</label><br>
                <input type="text" id="github_link" name="github_link"><br>

                <label for="screenshots">Project Screenshots / Code Snippets (Max 5 Images, JPG/PNG):</label><br>
                <input type="file" id="screenshots" name="screenshots[]" accept="image/jpeg,image/png" multiple required><br> 

                <input type="submit" name="submit_project" value="Submit Project for Approval">
            </form>

        <?php elseif ($view == 'list'): ?>

            <h2>📝 Your Submitted Projects</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Tech Stack</th>
                        <th>Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Senior ke apne projects dikhana
                    $user_id = $_SESSION['user_id'];
                    $sql_list = "SELECT title, tech_stack, year, status FROM projects WHERE uploaded_by = $user_id ORDER BY id DESC";
                    $result_list = $conn->query($sql_list);

                    if ($result_list->num_rows > 0) {
                        while($row = $result_list->fetch_assoc()) {
                            // Status color coding for better look
                            $status_color = ($row['status'] == 'Approved') ? 'green' : (($row['status'] == 'Rejected') ? 'red' : 'orange');
                            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tech_stack']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                            echo "<td style='color: {$status_color}; font-weight: bold;'>" . htmlspecialchars($row['status']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>You have not submitted any projects yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>