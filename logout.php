<?php
session_start();

// Destroy everything
session_unset();
session_destroy();

// Redirect to home page
header("Location: index.php");
exit;
?>
