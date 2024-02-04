<?php
$pageName = $_GET["name"];
$userID = "";
$userName = "";
$file = "../../Page/" . $pageName . "/main.html";
if (isset($_COOKIE["userID"])) {
    $file = "../../Page/" . $pageName . "/userMain.html";
}
$result = file_get_contents($file);

echo $result;
