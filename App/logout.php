<?php
require_once 'utils.php';

session_start();
destroy_session_and_data();
echo '<h1>You are now logged out</h1>';
echo '<a href="main.php">Click here to main page</a>';
header("refresh:2; url=main.php");
