<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

$year = strval(date("Y"));
$mouth = strval(date("m"));

if (isset($_GET["year"])) {
    $year = $_GET["year"];
}

if (isset($_GET["mouth"])) {
    $mouth = $_GET["mouth"];
}
require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$result = "";
//范围限制未加
//年
$sqlGet_day = "SELECT * FROM `mouth` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "'";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);
if (!empty($sqlGet_result)) {
    $result = "[income:" . $sqlGet_result[0]["income"] . "][pay:" . $sqlGet_result[0]["pay"] . "]";
}
echo $result;
