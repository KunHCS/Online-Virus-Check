<?php
// Run mannually only on first start up

require_once 'config.php';

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die($conn->connect_error);
}

$password = password_hash('admin', PASSWORD_BCRYPT);

$result = $conn->query("INSERT INTO admins (firstname, lastname, username, password) VALUES('default', 'default','admin', '$password')");

if (!$result) {
    die($conn->error);
}

if ($conn->connect_error) {
    die($conn->connect_error);
}

$conn->close();

echo '<h1>Initialized admin user</h1>';
