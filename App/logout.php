<?php
require_once 'utils.php';

session_start();
destroy_session_and_data();
echo '<h1>You are now logged out, redirecting to login</h1>';
echo '<a href="main.php">Click here to log in</a>';
header("refresh:3; url=main.php");
