<?php
session_start();
// Unset all of the session variables
$_SESSION = array();
session_destroy(); // Destroy the session
// Redirect to the login page
header("Location:index.php?logout=success");
exit(); // Ensure no further code is executed