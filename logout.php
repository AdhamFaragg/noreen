<?php
require_once 'db/config.php';
require_once 'includes/functions.php';

// Destroy session
session_destroy();

// Redirect to home
set_message('You have been logged out successfully.', 'success');
redirect(BASE_URL . 'index.php');
?>
