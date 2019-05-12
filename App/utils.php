<?php

function mysql_entities_fix_string($connection, $string)
{
    return htmlentities(mysql_fix_string($connection, $string));}

function mysql_fix_string($connection, $string)
{
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }

    return $connection->real_escape_string($string);
}

function destroy_session_and_data()
{
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

function phpRedirectSelf()
{
    $url = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
    header('Location: ' . $url); //prevent resubmission
    die();
}

function jsAlert($msg)
{
    $url = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
    echo <<<_END
    <script>
        alert("$msg");
        window.location.href = "$url";
    </script>
_END;
}
