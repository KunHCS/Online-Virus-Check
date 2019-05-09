<?php

require_once 'config.php';
require_once 'utils.php';

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

if (registerContributor($conn)) {return;}

echo <<< _END
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contributor Registration</title>
</head>

<body>
    <p class="text-center mb-0">
        <a class="text-center" href="main.php">Return to main page</a>
    </p>
    <form action="register.php" method="POST" autocomplete="off">

        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="submit" value="Register">
    </form>
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

    $temp_pw = password_hash($temp_pw, PASSWORD_BCRYPT);

    $statement = $connection->prepare("INSERT INTO contributors (username, password, email) VALUES(?,?,?)");
    $statement->bind_param('sss', $temp_un, $temp_pw, $temp_em);
    $statement->execute();
    if ($statement->error) {
        die("An error has occured, please try again.");
    }
    $statement->close();

    echo "<h1>successfully registered</h1>";
    return true;
}
