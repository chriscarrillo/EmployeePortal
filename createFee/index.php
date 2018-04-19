<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Fee</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
    <script type="text/javascript" src="../js/main.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php include "../php/functions.php"; ?>
<div id="fee">
    <?php
        session_start();
        if (!isLoggedIn()) {
            header("Location: login/index.php");
        }
    ?>
    
    <header>
        <h1>Employee Portal | <?php print $_SESSION["firstName"] . " " . $_SESSION["lastName"] . " (" . getEmployeeType() . ")"; ?></h1>
        <nav>
            <a href="..">Home</a>
            <a href="../courses">Courses</a>
            <a href="../profile/">Profile</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="content" class="clear">
        <h2>Create Fee</h2>
        <form action="" method="POST">
            <input type="text" name="feeType" placeholder="Fee Type" required /><br />
            <input type="text" name="feeAmount" placeholder="Fee Amount" required /><br />
            <input type="datetime-local" name="dueDate" required /><br />
            <input type="submit" name="createFee" value="Create Fee" />
        </form>
        
        <?php
            if (isset($_POST["createFee"])) {
                addFee($_GET["id"], $_POST["feeType"], $_POST["feeAmount"], $_POST["dueDate"]);
            }
        ?>
    </div>
</div>
</body>