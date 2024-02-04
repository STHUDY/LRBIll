<?php
if (!isset($_POST["userID"]) || !isset($_POST["passWord"]) || !isset($_POST["surePassword"])) {
    die("<script>ChageUserError()</script>");
}
$userID = $_POST["userID"];
$passWord = $_POST["passWord"];
$surePassword = $_POST["surePassword"];

if ($userID == "") {
    die("<script>window.parent.ChageUserState(6)</script>");
}

if (strlen($userID) < 6) {
    die("<script>window.parent.ChageUserState(11)</script>");
}

if (strlen($userID) > 32) {
    die("<script>window.parent.ChageUserState(12)</script>");
}

if ($passWord == "") {
    die("<script>window.parent.ChageUserState(7)</script>");
}

if (strlen($passWord) < 6) {
    die("<script>window.parent.ChageUserState(9)</script>");
}

if (strlen($passWord) > 16) {
    die("<script>window.parent.ChageUserState(10)</script>");
}

if ($surePassword == "") {
    die("<script>window.parent.ChageUserState(8)</script>");
}

if ($passWord != $surePassword) {
    die("<script>window.parent.ChageUserState(5)</script>");
}

require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$sqlCheck = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
$sqlCheckQuery = mysqli_query($sqlServer, $sqlCheck);
$sqlCheckResult = mysqli_fetch_all($sqlCheckQuery, MYSQLI_ASSOC);
if(!empty($sqlCheckResult)){
    die("<script>window.parent.ChageUserState(4)</script>");
}

$sql = "INSERT INTO `user` (`userID`, `userPassword`, `userName`, `name`, `pay`, `income`, `liabilities`, `lending`, `time`) VALUES (FROM_BASE64('" . base64_encode($userID) . "'), '" . sha1($passWord) . "', FROM_BASE64('" . base64_encode($userID) . "'), '[]', '0', '0', '0', '0', NOW());";
echo $sql;
mysqli_query($sqlServer, $sql);

setcookie("userID", rawurlencode($userID), time() + 24 * 60 * 60 * 30, "/");
setcookie("userName", rawurlencode($userID), time() + 24 * 60 * 60 * 30, "/");

echo "<script>window.parent.ChageUserState(2)</script>";