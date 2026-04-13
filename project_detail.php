<?php
session_start();
include 'db_connect.php'; 

$project = null;
$message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = (int)$_GET['id'];
    
    $sql = "SELECT p.*, u.name AS senior_name 
            FROM projects p
            JOIN users u ON p.uploaded_by = u.id
            WHERE p.id = $project_id";
            
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
        
        $screenshot_paths = [];
        if (!empty($project['screenshots'])) {
             $screenshot_paths = explode(',', $project['screenshots']);
             $project['screenshots_array'] = array_map('trim', $screenshot_paths);
        } else {
             $project['screenshots_array'] = []; 
        }

    } else {
        $message = "❌ Error: Project not found.";
    }
} else {
    $message = "❌ Error: Invalid project ID.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $project ? htmlspecialchars($project['title']) : 'Project Detail'; ?> | University Project Hub</title> 
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* CSS styles remains the same */
        .detail-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .detail-header {
            border-bottom: 3px solid #1e88e5;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .meta-info p {
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .screenshot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .screenshot-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .screenshot-item img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }
        .abstract-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 5px solid #28a745;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <h1>📚 University Project Hub</h1> </div>
            <div class="auth-links">
                <a href="index.php">Home</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <h1><?php echo $message; ?></h1>
        <?php elseif ($project): ?>
            <div class="detail-card">
                <div class="detail-header">
                    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
                    <span style="background-color: #1e88e5; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold;"><?php echo htmlspecialchars($project['status']); ?></span>
                </div>

                <div class="meta-info">
                    <p><strong>Submitted By:</strong> <?php echo htmlspecialchars($project['senior_name']); ?></p>
                    <p><strong>Year:</strong> <?php echo htmlspecialchars($project['year']); ?></p>
                    <p><strong>Tech Stack:</strong> <?php echo htmlspecialchars($project['tech_stack']); ?></p>
                    <?php if (!empty($project['github_link'])): ?>
                        <p><strong>GitHub:</strong> <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" class="github-link">View Repository</a></p>
                    <?php endif; ?>
                </div>

                <div class="abstract-section">
                    <h2>Abstract / Project Summary</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['abstract'])); ?></p>
                </div>
                
                <hr>

                <h2>Project Visuals (Screenshots & Code)</h2>
                <div class="screenshot-grid">
                    <?php 
                    if (!empty($project['screenshots_array'])) { 
                        foreach ($project['screenshots_array'] as $path) {
                            $path = trim($path); 
                            if (!empty($path) && file_exists($path)) {
                                echo "<div class='screenshot-item'>";
                                echo "<a href='{$path}' target='_blank'>"; 
                                echo "<img src='{$path}' alt='Project Screenshot'>";
                                echo "</a>";
                                echo "</div>";
                            } 
                        }
                    } else {
                        echo "<p>No visuals provided for this project.</p>";
                    }
                    ?>
                </div>

                <?php 
                if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && $project['status'] == 'Pending'): ?>
                    <hr>
                    <h2>Admin Actions</h2>
                    <p>Approve/Reject this project after reviewing details and visuals.</p>
                    <a href='admin_action.php?id=<?php echo $project['id']; ?>&action=approve' class='approve-btn'>Approve Project</a> 
                    <a href='admin_action.php?id=<?php echo $project['id']; ?>&action=reject' class='reject-btn'>Reject Project</a>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html>