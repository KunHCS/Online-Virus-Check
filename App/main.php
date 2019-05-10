<?php
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
</head>

<body>
    <div class='container'>
        <div class='row'>
            <div class='col-md-6 mx-auto mt-4'>
                <h2 class='mb-3'>Welcome to Online Virus Checker</h2>
                <a href="check.php">Run Check</a><br>
                <a href="upload.php">Upload New Malware</a><br>
                <a href="register.php">Regiter as Contributor</a><br>
            </div>
        </div>
    </div>
</body>

</html>
_END;
