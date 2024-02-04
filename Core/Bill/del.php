<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

$time = "";
if (isset($_POST["time"])) {
    $time = $_POST["time"];
}

$type = 0;
if (isset($_POST["type"])) {
    $type = intval($_POST["type"]);
} else {
    die();
}
$base = "bill";
if ($type == 2 || $type == 3) {
    $base = "loan";
}
$name = "";
if (isset($_POST["name"])) {
    $name = rawurlencode($_POST["name"]);
}
require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$sqlCheck = "SELECT * FROM `" . $base . "` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `time` = FROM_BASE64('" . base64_encode($time) . "')";
$sqlCheckQuery = mysqli_query($sqlServer, $sqlCheck);
$CheckResult = mysqli_fetch_all($sqlCheckQuery, MYSQLI_ASSOC);

$money = $CheckResult[0]["money"];
$type = $CheckResult[0]["type"];

$sqlDel_bill = "DELETE FROM `" . $base . "` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `time` = FROM_BASE64('" . base64_encode($time) . "')";
mysqli_query($sqlServer, $sqlDel_bill);

$sqlCheckBill = "SELECT * FROM `bill` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `name` = '" . $name . "'";

$sqlCheckBillQuery = mysqli_query($sqlServer, $sqlCheckBill);
$CheckBillResult = mysqli_fetch_all($sqlCheckBillQuery, MYSQLI_ASSOC);


$sqlGet_day = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "')";

$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);

$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];

if (empty($CheckBillResult)) {
    $nameArray = array();
    $newArray = array();
    $nameDel = false;
    if (empty($sqlGet_result["name"]) == false && $type != 2 && $type != 3) {
        $nameArray = json_decode($sqlGet_result["name"], true);
        foreach ($nameArray as $values) {
            if ($values == $name) {
                $nameDel = true;
            } else {
                array_push($newArray, $values);
            }
        }
    }

    if ($nameDel == true) {
        $sqlUserAddName = "UPDATE `user` SET `name`='" . json_encode($newArray) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
        mysqli_query($sqlServer, $sqlUserAddName);
    }
}

$sqlUser = "";
$money = $CheckResult[0]["money"];
if ($type == 0) {
    $money = $sqlGet_result["pay"] - $money;
    $sqlUser = "UPDATE `user` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 1) {
    $money = $sqlGet_result["income"] - $money;
    $sqlUser = "UPDATE `user` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 2) {
    $money = $sqlGet_result["liabilities"] - $money;
    $sqlUser = "UPDATE `user` SET `liabilities`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
} else if ($type == 3) {
    $money = $sqlGet_result["lending"] - $money;
    $sqlUser = "UPDATE `user` SET `lending`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "');";
}
mysqli_query($sqlServer, $sqlUser);

if ($type == 2 || $type == 3) {
    die();
}

$date = date_parse($time);

$year = $date["year"];
$mouth = $date["month"];
$day = $date["day"];

//天
$sqlGet_day = "SELECT * FROM `day` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "' AND `day` = '" . $day . "'";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];

$money = $CheckResult[0]["money"];
$sqlDay_chage = "";
if ($type == 0) {
    $money = $sqlGet_result["pay"] - $money;
    $sqlDay_chage = "UPDATE `day` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "' AND `day` = '" . $day . "'";
} else {
    $money = $sqlGet_result["income"] - $money;
    $sqlDay_chage = "UPDATE `day` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "' AND `day` = '" . $day . "'";
}
mysqli_query($sqlServer, $sqlDay_chage);

//月
$sqlGet_day = "SELECT * FROM `mouth` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "'";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];

$money = $CheckResult[0]["money"];
$sqlMouth_chage = "";
if ($type == 0) {
    $money = $sqlGet_result["pay"] - $money;
    $sqlMouth_chage = "UPDATE `mouth` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "'";
} else {
    $money = $sqlGet_result["income"] - $money;
    $sqlMouth_chage = "UPDATE `mouth` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "' AND `mouth` = '" . $mouth . "'";
}
mysqli_query($sqlServer, $sqlMouth_chage);

//年
$sqlGet_day = "SELECT * FROM `year` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";
$sqlGet_query = mysqli_query($sqlServer, $sqlGet_day);
$sqlGet_result = mysqli_fetch_all($sqlGet_query, MYSQLI_ASSOC)[0];

$money = $CheckResult[0]["money"];
$sqlYear_chage = "";
if ($type == 0) {
    $money = $sqlGet_result["pay"] - $money;
    $sqlYear_chage = "UPDATE `year` SET `pay`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";
} else {
    $money = $sqlGet_result["income"] - $money;
    $sqlYear_chage = "UPDATE `year` SET `income`='" . strval($money) . "' WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `year` = '" . $year . "'";
}
mysqli_query($sqlServer, $sqlYear_chage);
