<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Login</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
</head>
<body>
<?php include "../php/functions.php"; ?>
<div id="login">
    <?php
        session_start();
        if (isLoggedIn()) {
            print "<script type='text/javascript'>window.top.location='..';</script>";
        }
    ?>
    <h1>log in</h1>
        <form action="" method="post" id="loginForm" class="form">
            <input type="text" id="username" name="username" placeholder="username" autofocus="on" required /><br />
            <input type="password" id="password" name="password" placeholder="password" required /><br />
            <input type="submit" id="loginBtn" class="button" name="loginBtn" value="submit" />
        </form>
        <?php
            if (isset($_POST["loginBtn"])) {
                login($_POST["username"], $_POST["password"]);
            }
        ?>
</div>
</body>
</html>