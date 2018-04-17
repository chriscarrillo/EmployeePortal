<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Profile</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
</head>
<body>
<?php include "../php/functions.php"; ?>
<div id="home">
    <?php
        session_start();
        if (!isLoggedIn()) {
            header("Location: ../login/index.php");
        }
    ?>
    <header>
        <h1>Employee Portal | <?php print $_SESSION["firstName"] . " " . $_SESSION["lastName"] . " (" . getEmployeeType() . ")"; ?></h1>
        <nav>
            <a href="..">Home</a>
            <a href="../courses/">Courses</a>
            <a href="" class="currentPage">Profile</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="profile" class="clear">
        <h2>Edit Profile</h2>
        <p>
            <em>
                Note: Change any of the fields you please. Only system administrators, however, can change your employee type!
            </em>
        </p>
        <form action="" method="POST">
            <input type="text" id="firstName" name="firstName" value="<?php print $_SESSION["firstName"] ?>" required /><br />
            <input type="text" id="lastName" name="lastName" value="<?php print $_SESSION["lastName"] ?>" required /><br />
            <input type="text" id="username" name="username" value="<?php print $_SESSION["username"] ?>" required /><br />
            <input type="password" id="password" name="password" placeholder="Enter a new password" required /><br />
            <input type="submit" id="save" name="save" value="Save" />
        </form>
        <?php
            if (isset($_POST["save"])) {
                updateProfile($_SESSION["id"], $_POST["firstName"], $_POST["lastName"], $_POST["username"], $_POST["password"]);
            }
        ?>
    </div>
</div>
</body>
</html>