<?php

// $DB_Host = "localhost";
// $DB_Name = "bill";
// $DB_UserName = "Bill";
// $DB_UserPassword = "fbWeD2wEsKE52eD4";
// $DB_Port = 888;
if (isset($_GET["install"])) {
  $DB_Host = $_GET["DB_Host"];
  $DB_Name = $_GET["DB_Name"];
  $DB_UserName = $_GET["DB_UserName"];
  $DB_UserPassword = $_GET["DB_UserPassword"];
  $DB_Port = $_GET["DB_Port"];
  $stop = false;
  if ($DB_Host == "") {
    echo "<p style='color:red'>数据库地址为空</p>";
    $stop = true;
  }
  if ($DB_Name == "") {
    echo "<p style='color:red'>数据库名址为空</p>";
    $stop = true;
  }
  if ($DB_UserName == "") {
    echo "<p style='color:red'>数据库用户名为空</p>";
    $stop = true;
  }
  if ($DB_UserPassword == "") {
    echo "<p style='color:red'>数据库密码为空</p>";
    $stop = true;
  }
  if ($DB_Port == "") {
    echo "<p style='color:red'>数据库端口为空</p>";
    $stop = true;
  }

  if ($stop == true) {
    die("<a href='?'>重新安装</a><h1 style='color:red'>安装失败</h1>");
  }
  $value = '<?php' . PHP_EOL . '$DB_Host = "' . $DB_Host . '";' . PHP_EOL . '$DB_Name = "' . $DB_Name . '";' . PHP_EOL . '$DB_UserName = "' . $DB_UserName . '";' . PHP_EOL . '$DB_UserPassword = "' . $DB_UserPassword . '";' . PHP_EOL . '$DB_Port = ' . $DB_Port . ';';
  file_put_contents("./Core/Config/config.php", $value);
  $dateBase = mysqli_connect($DB_Host, $DB_UserName, $DB_UserPassword, $DB_Name, $DB_Port);
  if (mysqli_error($dateBase)) {
    die("<p style='color:red'>数据库连接失败</p><h1 style='color:green'>安装失败</h1>");
  }
  $sql[0] = 'CREATE TABLE bill (
      userID char(32) NOT NULL,
      money float(36,2) NOT NULL,
      value text NOT NULL,
      name text NOT NULL,
      type tinyint(1) NOT NULL,
      time datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
  $sql[1] = 'CREATE TABLE `day` (
    userID char(32) NOT NULL,
    day int(11) NOT NULL,
    pay float(36,2) NOT NULL,
    income float(36,2) NOT NULL,
    mouth int(11) NOT NULL,
    year int(11) NOT NULL,
    time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
  $sql[2] = 'CREATE TABLE loan (
    userID char(32) NOT NULL,
    money float NOT NULL,
    value text NOT NULL,
    repay int(11) NOT NULL,
    rate float NOT NULL,
    form tinyint(11) NOT NULL,
    name text NOT NULL,
    type tinyint(1) NOT NULL,
    startTime date NOT NULL,
    endTime date NOT NULL,
    time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
  $sql[3] = 'CREATE TABLE mouth (
    userID char(32) NOT NULL,
    mouth int(11) NOT NULL,
    pay float(36,2) NOT NULL,
    income float(36,2) NOT NULL,
    year int(11) NOT NULL,
    time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
  $sql[4] = 'CREATE TABLE `user` (
    userID char(32) NOT NULL,
    userName text NOT NULL,
    name mediumtext NOT NULL,
    pay float(36,2) UNSIGNED NOT NULL,
    income float(36,2) UNSIGNED NOT NULL,
    liabilities float(36,2) UNSIGNED NOT NULL,
    lending float(36,2) UNSIGNED NOT NULL,
    time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
  $sql[5] = 'CREATE TABLE `year` (
    userID char(32) NOT NULL,
    year int(11) NOT NULL,
    pay float(36,2) NOT NULL,
    income float(36,2) NOT NULL,
    time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

  $sql[6] = 'ALTER TABLE bill
      ADD UNIQUE KEY time (time),
      ADD KEY time_2 (time);';

  $sql[7] = 'ALTER TABLE `day`
      ADD UNIQUE KEY `time` (`time`),
      ADD KEY `time_2` (`time`);';

  $sql[8] = 'ALTER TABLE `loan`
      ADD UNIQUE KEY `time` (`time`),
      ADD KEY `time_2` (`time`);';

  $sql[9] = 'ALTER TABLE `mouth`
      ADD UNIQUE KEY `time` (`time`),
      ADD KEY `time_2` (`time`);';

  $sql[10] = 'ALTER TABLE `user`
      ADD UNIQUE KEY `time` (`time`),
      ADD UNIQUE KEY `userID` (`userID`),
      ADD KEY `time_2` (`time`);';

  $sql[11] = 'ALTER TABLE `year`
      ADD UNIQUE KEY `time` (`time`),
      ADD KEY `time_2` (`time`);';
  for ($i = 0; $i < 11; $i++) {
    mysqli_query($dateBase, $sql[$i]);
  }
  echo "<h1 style='color:green'>安装成功</h1>";
  die("<script>setTimeout(() => {
        window.location.href = \" / \";
    }, 1500);</script>");
}

?>
<html>

<head>
  <title>LRBill安装程序</title>
</head>

<body>
  <h1>数据库设置</h1>
  <table>
    <tbody>
      <tr>
        <td>数据库地址：</td>
        <td><input id="DB_Host" type="text" value="localhost" /></td>
      </tr>
      <tr>
        <td>数据库名：</td>
        <td><input id="DB_Name" type="text" /></td>
      </tr>
      <tr>
        <td>数据库用户名：</td>
        <td><input id="DB_UserName" type="text" /></td>
      </tr>
      <tr>
        <td>数据库密码：</td>
        <td><input id="DB_UserPassword" type="text" /></td>
      </tr>
      <tr>
        <td>数据库端口：</td>
        <td><input id="DB_Port" type="number" value="888" /></td>
      </tr>
    </tbody>
  </table>
  <br>
  <button onclick="install()">安装</button>
  <script>
    function install() {
      window.location.href = "?install&DB_Host=" + document.getElementById("DB_Host").value + "&DB_Name=" + document.getElementById("DB_Name").value + "&DB_UserName=" + document.getElementById("DB_UserName").value + "&DB_UserPassword=" + document.getElementById("DB_UserPassword").value + "&DB_Port=" + document.getElementById("DB_Port").value;

    }
  </script>
</body>

</html>