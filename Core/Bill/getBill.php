<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}
$value = "";
$time = "";
$type = 0;
$number = "0";
if (isset($_GET["time"])) {
    $time = rawurldecode($_GET["time"]);
}
if (isset($_GET["value"])) {
    $value = rawurldecode($_GET["value"]);
}
if (isset($_GET["type"])) {
    $type = intval($_GET["type"]);
}
if (isset($_GET["number"])) {
    $number = intval($_GET["number"]);
}

$number *= 30;
$time .= "-";
$base = "bill";
if ($type == 1) {
    $base = "loan";
}
require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();
$name = "";
if ($value != "") {
    $name = rawurlencode($value);
}
$sqlCheck = "SELECT * FROM `" . $base . "` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `time` LIKE '%" . $time . "%' AND `value` LIKE '%" . $value . "%' OR `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `name` LIKE '%" . $name . "%' AND `time` LIKE '%" . $time . "%' OR `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `money` = '" . $value . "' AND `time` LIKE '%" . $time . "%' ORDER BY `time` DESC LIMIT 30 OFFSET " . strval($number) . ";";
//echo $sqlCheck;
$sqlCheckQuery = mysqli_query($sqlServer, $sqlCheck);
$CheckResult = mysqli_fetch_all($sqlCheckQuery, MYSQLI_ASSOC);

if (empty($CheckResult)) {
    die();
}

$result = "";
foreach ($CheckResult as $value) {

    $result .= "[money:" . $value['money'] . "]";
    $result .= "[value:" . $value['value'] . "]";
    $result .= "[name:" . $value['name'] . "]";
    $result .= "[type:" . $value['type'] . "]";
    if ($type == 1) {
        $result .= "[startTime:" . $value['startTime'] . "]";
        $result .= "[endTime:" . $value['endTime'] . "]";
    }
    $result .= "[time:" . $value['time'] . "];";
}
echo $result;
