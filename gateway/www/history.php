<?php
session_start();
include 'utilities.php';
connect_to_id_store();
verify_login();

?>
<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>
