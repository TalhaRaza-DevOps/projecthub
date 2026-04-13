<style>
    /* CSS for dashboard layout */
    .admin-main { display: flex; min-height: 100vh; }
    .admin-sidebar {
        width: 250px;
        background-color: #2c3e50; /* Darker blue/grey for admin */
        color: white;
        padding: 15px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.2);
    }
    .admin-sidebar h3 { color: #fff; border-bottom: 2px solid #5a7082; padding-bottom: 10px; }
    .admin-sidebar a {
        color: #aebecd;
        text-decoration: none;
        display: block;
        padding: 12px 10px;
        margin-bottom: 5px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .admin-sidebar a:hover, .admin-sidebar .active {
        color: white;
        background-color: #3f5971;
    }
    .admin-content {
        flex-grow: 1; 
        padding: 20px;
        background-color: #f8f9fa;
    }
</style>

<div class="admin-sidebar">
    <h3>Admin Panel (<?php echo $_SESSION['name']; ?>)</h3>
    <a href="admin_dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : ''; ?>">⚙️ Pending Projects</a>
    <a href="admin_approved.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_approved.php') ? 'active' : ''; ?>">✅ Approved Archive</a>
    <a href="admin_users.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_users.php') ? 'active' : ''; ?>">👥 Student Verification</a>
    <hr style="border-color: #5a7082;">
    
    <a href="index.php">🌍 Public Hub Home</a>
    
    <hr style="border-color: #5a7082;">
    <a href="logout.php" style="color: #ffcccc;">🚪 Logout</a>
</div>