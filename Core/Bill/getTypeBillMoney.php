<?php
$userID = "";
if (isset($_COOKIE["userID"])) {
    $userID = $_COOKIE["userID"];
} else {
    die();
}

$year = date("Y");
$mouth = date("m");

$dateYearMouth = $year . "-" . $mouth;

require_once("../Function/ConnectDatebase.php");
$sqlServer = ConnectDatebase();

$sqlGet = "SELECT * FROM `user` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "')";
$sqlQuery = mysqli_query($sqlServer, $sqlGet);
$sqlResult = mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC)[0];

$nameArray = array();
$nameArray = json_decode($sqlResult["name"], true);
$result = "";
foreach ($nameArray as $value) {
    for ($i = 0; $i < 2; $i++) {
        $type = strval($i);
        $sql = "SELECT * FROM `bill` WHERE `userID` = FROM_BASE64('" . base64_encode($userID) . "') AND `name` = '" . $value . "'  AND `type` = '" . $type . "' AND `time` LIKE '%" . $dateYearMouth . "%'";
        $query = mysqli_query($sqlServer, $sql);
        $findDate = mysqli_fetch_all($query, MYSQLI_ASSOC);
        if (empty($findDate)) {
            continue;
        }
        $money = 0;
        foreach ($findDate as $date) {
            $money += $date["money"];
        }
        $result .= "[money:" . strval($money) . "]";
        $result .= "[name:" . $value . "]";
        $result .= "[type:" . $type . "];";
    }
}
echo $result;
