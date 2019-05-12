<?php

require_once 'config.php';
require_once 'utils.php';

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die($conn->connect_error);
}

if (isset($_FILES['File'])) {
    malwareCheck($conn);
}

$conn->close();

//form
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
    <title>Online Virus Checker</title>
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
                <h2 class="text-center">Online Virus Checker</h2>
                <p class="text-center">
                    <a class="text-center" href="main.php">Return to main page</a>
                </p>
                <form class="px-4 py-4 border" action="check.php" method="post" autocomplete="off"
                    enctype='multipart/form-data'>
                    <div class="form-group">
                        <label for="file">File</label>
                        <input class="form-control-file" type="file" name="File" required />
                    </div>
                    <input type="submit" value='Check' class="btn btn-sm btn-primary btn-block">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
_END;

function malwareCheck($conn)
{
    if (!is_uploaded_file($_FILES['File']['tmp_name'])) {
        return false;
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
        jsAlert('Empty File Signature');
        die();
    }

    $query = "SELECT signature FROM malware_admin";
    $result = $conn->query($query);
    if (!$result) {
        die("Database access failed: " . $conn->error);
    }
    $found = false;

    for ($i = 0; $i < $result->num_rows && !$found; $i++) {
        $result->data_seek($i);
        $temp_assoc = $result->fetch_assoc();
        if ($signature == $temp_assoc['signature']) {
            $found = true;
        }
    }
    if ($found) {
        echo "<h1 class='text-center'>Virus Detected</h1>";
        return;
    }
    echo "<h1 class='text-center'>No Virus Detected</h1>";

}
