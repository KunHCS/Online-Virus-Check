<?php

require_once 'config.php';
require_once 'utils.php';

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

echo <<< _END
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
        crossorigin="anonymous">
    <title>Contributor Registration</title>
</head>
_END;

if (registerContributor($conn)) {
    header("refresh:2; url=main.php");
    die();
}

echo <<< _END
<body>
    <form class="mt-2 ml-1" action="register.php" method="POST" autocomplete="off">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="submit" value="Register">
    </form>
    <p class="mt-1 ml-1">
        <a href="main.php">Return to main page</a>
    </p>
</body>

</html>
_END;

function registerContributor($connection)
{
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email'])) {
        return false;
    }
    $temp_un = mysql_entities_fix_string($connection, $_POST['username']);
    $temp_pw = mysql_entities_fix_string($connection, $_POST['password']);
    $temp_em = mysql_entities_fix_string($connection, $_POST['email']);

    if (!preg_match_all('/^[a-zA-Z_-]+$/', $temp_un) || empty($temp_un)) {
        die('Invalid/Empty Username');
    }
    if (!filter_var($temp_em, FILTER_VALIDATE_EMAIL) || empty($temp_em)) {
        die('Invalid/Empty Email');
    }

    $temp_pw = password_hash($temp_pw, PASSWORD_BCRYPT);

    $statement = $connection->prepare("INSERT INTO contributors (username, password, email) VALUES(?,?,?)");
    $statement->bind_param('sss', $temp_un, $temp_pw, $temp_em);
    $statement->execute();
    if ($statement->error) {
        die("An error has occured, please try again.");
    }
    $statement->close();

    echo "<h1 class='text-center'>Successfully registered</h1>";
    return true;
}
