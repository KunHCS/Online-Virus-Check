<?php

require_once 'config.php';
require_once 'utils.php';

ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 60 * 30);
session_start();
session_regenerate_id();

if (isset($_SESSION['username'])) {
    if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .
        $_SERVER['HTTP_USER_AGENT'])) {
        destroy_session_and_data();
        echo "encountered technical error, please log in again";
        echo "<a href='login.php'>Click here to log in</a>";
        return;
    }

    $username = $_SESSION['username'];
    $uid = $_SESSION['id'];
    $role = $_SESSION['role'];

} else {
    echo "<h1>You are not logged in</h1>";
    echo "<a href='login.php'>Click here to log in</a>";
    header("refresh:1.5; url=login.php");
    die();
}

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

uploadFile($conn, $role, $uid);

$conn->close();

echo <<<_END
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
        <script src="validate.js"></script>
    <title>Malware Upload</title>
    <style>
        label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-5 mt-4 mx-auto">
                <h2 class="text-center">Welcome, $username</h2>
                <h5 class="text-center">Role: $role</h5>
                <p class="text-center mb-0">
                    <a class="text-center" href="logout.php">Logout</a>
                </p>
                <p class="text-center">
                    <a class="text-center" href="main.php">Return to main page</a>
                </p>

                <form class="px-4 py-5 border" action="upload.php" method="post" autocomplete="off"
                    enctype='multipart/form-data' onsubmit="return validateMalwareForm(this)">
                    <div class="form-group">
                        <label for="name">Malware Name</label>
                        <input class="form-control" type="text" name="Name"
                            placeholder="Malware Name" required>
                    </div>
                    <div class="form-group">
                        <label for="file">File</label>
                        <input class="form-control-file" type="file" name="File" required />
                    </div>
                    <input type="submit" value='Upload' class="btn btn-sm btn-primary btn-block">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
_END;

function uploadFile($conn, $role, $id)
{
    if (isset($_POST['Name']) && is_uploaded_file($_FILES['File']['tmp_name'])) {

        $temp_Name = mysql_entities_fix_string($conn, $_POST['Name']);
        $regex = '/^[a-zA-Z0-9]+$/';
        if (!preg_match_all($regex, $temp_Name) || empty($temp_Name)) {
            jsAlert('Invalid/Empty Malware Name');
            die();
        }

        $query = $role == 'admin' ? "INSERT INTO malware_admin (malware_name, signature, admin_id) VALUES(?,?,?)" :
        "INSERT INTO malware_contrib (malware_name, signature, contributor_id) VALUES(?,?,?)";

        $statement = $conn->prepare($query);
        if ($conn->connect_error) {
            $statement->close();
            die($conn->connect_error);
        }

        $fh = fopen($_FILES['File']['tmp_name'], "r") or die("Failed to open file");
        if (flock($fh, LOCK_SH)) {
            $signature = fread($fh, 20);
            flock($fh, LOCK_UN);
        }
        fclose($fh);

        $signature = mysql_entities_fix_string($conn, $signature);

        if (empty($signature)) {
            $conn->close();
            $statement->close();
            jsAlert('Empty File Signature');
            die();
        }

        $statement->bind_param('ssi', $temp_Name, $signature, $id);

        $statement->execute();
        if ($statement->error) {
            $conn->close();
            die($statement->error);
        }
        $statement->close();
        jsAlert("Upload successful");
    }
}
