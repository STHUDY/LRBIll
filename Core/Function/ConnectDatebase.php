<?php
function ConnectDatebase()
{
    require_once("../Config/config.php");
    $dateBase = mysqli_connect($DB_Host, $DB_UserName, $DB_UserPassword, $DB_Name, $DB_Port);
    if (mysqli_error($dateBase)) {
        $dateBase = null;
    }
    return $dateBase;
}
