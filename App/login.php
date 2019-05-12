<?php

require_once 'config.php';
require_once 'utils.php';

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

if (authenticate($conn)) {
    $conn->close();
    return;
}

$conn->close();

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
    <title>Admin/Contributor Login</title>
</head>

<body>

    <form class="mt-2 ml-1" action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role">
            <option value="contributor">contributor</option>
            <option value="admin">admin</option>
        </select>
        <input type="submit" name="submit" value="Log In">
    </form>
    <p class="mt-2 ml-1">
        <a href="main.php">Return to main page</a>
    </p>
</body>

</html>
_END;

function authenticate($connection)
{
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['role'])) {
        return false;
    }
    $temp_role = $_POST['role'];
    if ($temp_role == 'admin') {
        $table = 'admins';
    } elseif ($temp_role == 'contributor') {
        $table = 'contributors';
    } else {
        die('invalid role');
    }

    $temp_un = mysql_entities_fix_string($connection, $_POST['username']);
    $temp_pw = mysql_entities_fix_string($connection, $_POST['password']);
    $statement = $connection->prepare("SELECT * FROM $table WHERE username=?");
    $statement->bind_param('s', $temp_un);
    $statement->execute();
    $result = $statement->get_result();
    if ($statement->error) {
        die("An error has occured, please try again.");
    }
    if ($result->num_rows) {
        $result->data_seek(0);
        $assoc = $result->fetch_assoc();
        $result->close();

        $valid = password_verify($temp_pw, $assoc['password']);
        if ($valid) {
            session_start();
            $_SESSION['username'] = $temp_un;
            $_SESSION['role'] = $temp_role;
            $_SESSION['id'] = $assoc['id'];
            $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] .
                $_SERVER['HTTP_USER_AGENT']);

            $un = $assoc['username'];
            echo "<h1>Hello $un, you are now logged in</h1>";
            echo "<a href='upload.php'>click here to continue</a>";
            header("refresh:1.5; url=upload.php");
            return true;
        } else {
            jsAlert("invalid username/password for $temp_role");
            die();
        }
    } else {
        jsAlert("invalid username/password for $temp_role");
        die();
    }
    return false;
}
