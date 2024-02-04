<?php
if (isset($_COOKIE['userID'])) {
    setcookie("userID", "", time() - 1, "/");
}
if (isset($_COOKIE['userName'])) {
    setcookie("userName", "", time() - 1, "/");
}
?>

<html>

<head>
    <meta charset="utf-8">
    <title>退出登录</title>
    <link rel="icon" href="https://source.sober-up.cn/Img/Icon/LRBill.ico" type="image/x-icon">
    <link rel="shortcut icon" href="https://source.sober-up.cn/Img/Icon/LRBill.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @media screen and (prefers-color-scheme: dark) {
            body {
                background-color: black;
            }

            h1 {
                color: white;
            }
        }
    </style>
</head>

<body>
    <h1>退出成功！</h1>
    <script>
        setTimeout(function() {
            window.location.href = "/#Main";
        }, 1000);
    </script>
</body>

</html>