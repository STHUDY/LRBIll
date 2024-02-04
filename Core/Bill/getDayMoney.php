<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

$year = strval(date("Y"));
$mouth = strval(date("m"));
$day = strval(date("d"));
if (isset($_GET["year"])) {
    $year = $_GET["year"];
}

if (isset($_GET["mouth"])) {
    $mouth = $_GET["mouth"];
}

if (isset($_GET["day"])) {
    $day = $_GET["day"];
}
require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$result = "";
//范围限制未加
//年
$sqlGet_day = "SELECT * FROM `day` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "' AND `day` = '" . $day . "'";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);
if (!empty($sqlGet_result)) {
    $result = "[income:" . $sqlGet_result[0]["income"] . "][pay:" . $sqlGet_result[0]["pay"] . "]";
}
echo $result;
