<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$result = "";
//范围限制未加
//年
$sqlGet_day = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "')";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);
if (!empty($sqlGet_result)) {
    $result = "[income:" . $sqlGet_result[0]["income"] . "][pay:" . $sqlGet_result[0]["pay"] . "][liabilities:" . $sqlGet_result[0]["liabilities"] . "][lending:" . $sqlGet_result[0]["lending"] . "]";
}
echo $result;