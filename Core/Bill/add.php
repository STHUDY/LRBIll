<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

sleep(1);

$money = "";
$value = "";
$name = "";
$type = "";

if (isset($_POST["money"])) {
    $money = floatval($_POST["money"]);
} else {
    die("<script>window.parent.ChageAddError()</script>");
}
if (isset($_POST["value"])) {
    $value = $_POST["value"];
}
if (isset($_POST["name"])) {
    $name = rawurlencode($_POST["name"]);
} else {
    die("<script>window.parent.ChageAddError()</script>");
}
if (isset($_POST["type"])) {
    $type = intval($_POST["type"]);
} else {
    die("<script>window.parent.ChageAddError()</script>");
}
if ($money == 0) {
    die("<script>window.parent.ChageAddError()</script>");
}
if ($name == "") {
    die("<script>window.parent.ChageAddError()</script>");
}
$money_back = 0;
if ($money < 0 && $type == 1) {
    $type = 0;
} else if ($money < 0 && $type == 0) {
    $type = 1;
} else if ($money < 0 && $type == 2) {
    $type = 3;
} else if ($money < 0 && $type == 3) {
    $type = 2;
}
$nowTime = date("Y-m-d H:i:s");
$numberTime = strtotime($nowTime);
$money_back = abs($money);
$money = $money_back;
require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

//用户
$sqlGet_day = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];

$nameArray = array();
$nameExist = false;
if (empty($sqlGet_result["name"]) == false && $type != 2 && $type != 3) {
    $nameArray = json_decode($sqlGet_result["name"], true);
    foreach ($nameArray as $values) {
        if ($values == $name) {
            $nameExist = true;
            break;
        }
    }
} else {
    $nameExist = true;
}

if ($nameExist != true) {
    //die("2"); 
    array_push($nameArray, $name);
    $sqlUserAddName = "UPDATE `user` SET `name`='" . json_encode($nameArray) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
    mysqli_query($sqlServer, $sqlUserAddName);
}

$sqlUser = "";
$money = $money_back;
if ($type == 0) {
    $money += $sqlGet_result["pay"];
    $sqlUser = "UPDATE `user` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 1) {
    $money += $sqlGet_result["income"];
    $sqlUser = "UPDATE `user` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 2) {
    $money += $sqlGet_result["liabilities"];
    $sqlUser = "UPDATE `user` SET `liabilities`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 3) {
    $money += $sqlGet_result["lending"];
    $sqlUser = "UPDATE `user` SET `lending`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
}

if ($type == 2 || $type == 3) {
    $startTime = $_POST["startTime"];
    $endTime = $_POST["endTime"];
    if ($startTime == "") {
        die("<script>window.parent.ChageAddError()</script>");
    }
    if ($endTime == "") {
        die("<script>window.parent.ChageAddError()</script>");
    }
    mysqli_query($sqlServer, $sqlUser);
    $money = $money_back;
    $sql_loan = "INSERT INTO `loan`(`userID`, `money`, `value`, `repay`, `rate`, `form`, `name`, `type`, `startTime`, `endTime`, `time`) VALUES  (FROM_BASE64('" . base64_encode($userID) . "'), '" . strval($money) . "', '" . $value . "', '0', '0', '0', '" . $name . "', '" . strval($type) . "', '" . $startTime . "', '" . $endTime . "', '" . $nowTime . "')";
    mysqli_query($sqlServer, $sql_loan);
    die("<script>window.parent.ChageAddState()</script>");
}

mysqli_query($sqlServer, $sqlUser);
$money = $money_back;

$sql_bill = "INSERT INTO `bill` (`userID`, `money`, `value`, `name`, `type`, `time`) VALUES (FROM_BASE64('" . base64_encode($userID) . "'), '" . strval($money) . "', '" . $value . "', '" . $name . "', '" . strval($type) . "', '" . $nowTime . "')";

mysqli_query($sqlServer, $sql_bill);

$year = date("Y", $numberTime);
$mouth = date("m", $numberTime);
$day = date("d", $numberTime);

$sqlGet_day = "SELECT * FROM `day` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `day` = '" . $day . "' AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);

$sql_day = "";

if (empty($sqlGet_result)) {
    $sql_day = "INSERT INTO `day` (`userID`, `day`, `pay`, `income`, `mouth`, `year`, `time`) VALUES (FROM_BASE64('" . base64_encode($userID) . "'), '" . $day . "', '0', '0', '" . $mouth . "', '" . $year . "', '" . $nowTime . "');";
    mysqli_query($sqlServer, $sql_day);
}

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];
if ($type == 0) {
    $money += $sqlGet_result["pay"];

    $sql_day = "UPDATE `day` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `day` = '" . $day . "' AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";
} else {
    $money += $sqlGet_result["income"];
    $sql_day = "UPDATE `day` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `day` = '" . $day . "' AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";
}
mysqli_query($sqlServer, $sql_day);

//月
$sqlGet_day = "SELECT * FROM `mouth` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);

$sql_day = "";
$money = $money_back;
if (empty($sqlGet_result)) {
    $sql_day = "INSERT INTO `mouth` (`userID`, `mouth`, `pay`, `income`, `year`, `time`) VALUES (FROM_BASE64('" . base64_encode($userID) . "'), '" . $mouth . "', '0', '0', '" . $year . "', '" . $nowTime . "');";
    mysqli_query($sqlServer, $sql_day);
}

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];
if ($type == 0) {
    $money += $sqlGet_result["pay"];

    $sql_day = "UPDATE `mouth` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";
} else {
    $money += $sqlGet_result["income"];
    $sql_day = "UPDATE `mouth` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `mouth` = '" . $mouth . "' AND `year` = '" . $year . "'";
}
mysqli_query($sqlServer, $sql_day);

//年
$sqlGet_day = "SELECT * FROM `year` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC);

$sql_day = "";
$money = $money_back;
if (empty($sqlGet_result)) {
    $sql_day = "INSERT INTO `year` (`userID`, `year`, `pay`, `income`, `time`) VALUES (FROM_BASE64('" . base64_encode($userID) . "'), '" . $year . "', '0', '0', '" . $nowTime . "');";
    mysqli_query($sqlServer, $sql_day);
}

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];
if ($type == 0) {
    $money += $sqlGet_result["pay"];
    $sql_day = "UPDATE `year` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";
} else {
    $money += $sqlGet_result["income"];
    $sql_day = "UPDATE `year` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";
}
mysqli_query($sqlServer, $sql_day);
die("<script>window.parent.ChageAddState()</script>");
