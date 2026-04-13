<style>
    /* CSS for senior dashboard layout (same as admin) */
    .senior-main { display: flex; min-height: 100vh; }
    .senior-sidebar {
        width: 250px;
        background-color: #4a8069; /* Slightly different color for senior, like a dark green */
        color: white;
        padding: 15px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.2);
    }
    .senior-sidebar h3 { color: #fff; border-bottom: 2px solid #77a08a; padding-bottom: 10px; }
    .senior-sidebar a {
        color: #d1e2da;
        text-decoration: none;
        display: block;
        padding: 12px 10px;
        margin-bottom: 5px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .senior-sidebar a:hover, .senior-sidebar .active {
        color: white;
        background-color: #5c977b;
    }
    .senior-content {
        flex-grow: 1; /* Content area puri jagah lega */
        padding: 20px;
        background-color: #f8f9fa;
    }
    /* Rest of the styles (table, form, etc.) will come from style.css */
</style>

<div class="senior-sidebar">
    <h3>Senior Menu (<?php echo $_SESSION['name']; ?>)</h3>
    <a href="senior_dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'senior_dashboard.php' && !isset($_GET['view'])) ? 'active' : ''; ?>">🏠 Submit New Project</a>
    <a href="senior_dashboard.php?view=list" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'senior_dashboard.php' && isset($_GET['view']) && $_GET['view'] == 'list') ? 'active' : ''; ?>">📝 My Submissions</a>
    <hr style="border-color: #77a08a;">
    
    <a href="index.php">🌍 Public Hub Home</a>
    
    <hr style="border-color: #77a08a;">
    <a href="logout.php" style="color: #ffcccc;">🚪 Logout</a>
</div>