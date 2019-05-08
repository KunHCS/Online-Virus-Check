<?php

require_once 'config.php';
require_once 'utils.php';

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die($conn->connect_error);
}

//form
echo <<<_END
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form method="post" action="check.php">
        Select File: <input type="file" name="uploded_filename" />
        <input type="submit" name="fileUpload" value="Check" />
        <button type="submit"><a href="main.php"> Click to back HomePage</button>
    </form>
</body>

</html>
_END;
