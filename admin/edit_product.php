<?php
// This file serves both add and edit operations
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();
require_admin();

include 'add_product.php';
?>
