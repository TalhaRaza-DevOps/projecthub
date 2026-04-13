<?php
session_start();
// Sari session variables ko khatam karo
session_unset();
// Session destroy karo
session_destroy();
// User ko wapis login page par bhej do
header("Location: login.php");
exit();
?>
