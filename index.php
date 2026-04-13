<?php
include 'db_connect.php'; 

// Search variables ko initialize
$search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : '';

// Base SQL Query (Sirf Approved projects ke liye)
$sql = "SELECT p.id, p.title, p.abstract, p.tech_stack, p.year, p.github_link, u.name AS senior_name 
        FROM projects p
        JOIN users u ON p.uploaded_by = u.id
        WHERE p.status = 'Approved'"; 

// Search Term ko Query mein add karna
if (!empty($search_term)) {
    $sql .= " AND (p.title LIKE '%$search_term%' OR p.tech_stack LIKE '%$search_term%')";
}

// Year Filter ko Query mein add karna
if (!empty($filter_year)) {
    $sql .= " AND p.year = '$filter_year'";
}

$sql .= " ORDER BY p.year DESC, p.title ASC"; 
$result = $conn->query($sql);

// Year Filter ke Dropdown ke liye Approved Years nikalna
$years_query = "SELECT DISTINCT year FROM projects WHERE status = 'Approved' ORDER BY year DESC";
$years_result = $conn->query($years_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Archive | University Project Hub</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <h1>📚 University Project Hub</h1>
            </div>
            <div class="auth-links">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </header>

    <div class="index-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>🎓 University Project Repository</h1>
            <p><strong>Welcome!</strong> Explore, filter, and gain insights from approved Final Year Projects submitted by our students.</p>
        </div>
        
        <div class="search-container">
            
            <form method="GET" action="index.php" class="search-form">
                
                <input type="text" name="search" placeholder="Search Title or Tech Stack..." value="<?php echo htmlspecialchars($search_term); ?>">
                
                <select name="year">
                    <option value="">Filter Year</option>
                    <?php
                    if ($years_result->num_rows > 0) {
                        while($year_row = $years_result->fetch_assoc()) {
                            $selected = ($year_row['year'] == $filter_year) ? 'selected' : '';
                            echo "<option value='{$year_row['year']}' $selected>{$year_row['year']}</option>";
                        }
                    }
                    ?>
                </select>
                
                <input type="submit" value="Search">
            </form>

            <div class="filter-tags">
                <a href="index.php?search=LMS">#LMS</a>
                <a href="index.php?search=Ticketing">#Online Ticketing</a>
                <a href="index.php?search=Management">#Management System</a>
                <a href="index.php?search=eCommerce">#eCommerce</a>
                <a href="index.php?search=Mobile">#Mobile Apps</a>
                <?php if (!empty($search_term) || !empty($filter_year)): ?>
                    <a href="index.php" style="background-color: #f0f0f0;">Clear Filters</a>
                <?php endif; ?>
            </div>
        </div>

        <hr style="margin-top: 0; margin-bottom: 30px;">

        <h2>Approved Projects Showcase</h2>
        
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<a href='project_detail.php?id=" . $row['id'] . "' style='text-decoration:none; color:inherit;'>"; 
                echo "<div class='project-card'>";
                echo "<h3>" . htmlspecialchars($row['title']) . " (" . $row['year'] . ")</h3>";
                echo "<p><strong>Submitted By:</strong> " . htmlspecialchars($row['senior_name']) . "</p>";
                echo "<p><strong>Tech Stack:</strong> " . htmlspecialchars($row['tech_stack']) . "</p>";
                echo "<p>" . substr(htmlspecialchars($row['abstract']), 0, 150) . "... <span style='color:#1e88e5; font-weight:bold;'>View Details</span></p>"; 
                echo "</div>";
                echo "</a>";
            }
        } else {
            echo "<p>❌ No projects found matching your criteria. Try different search terms or filters.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>