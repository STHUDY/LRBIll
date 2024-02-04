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

$sql = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "')";
$query = mysqli_query($sqlServer, $sql);
$sqlResult = mysqli_fetch_all($query, MYSQLI_ASSOC)[0];
if (!empty($sqlResult)) {
    $numberTime = strtotime($sqlResult["time"]);
    $result = date("Y", $numberTime);
}
echo $result;