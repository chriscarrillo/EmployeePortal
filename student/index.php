<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Student</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
    <script type="text/javascript" src="../js/main.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php include "../php/functions.php"; ?>
<div id="home">
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
        <h2>Edit Student</h2>
        <form action="" method="POST" id="editStudent">
            <input type="text" name="firstName" value="<?= $_GET["firstName"] ?>" required /><br />
            <input type="text" name="lastName" value="<?= $_GET["lastName"] ?>" required /><br />
            <select id="studentAdvisor" name="advisor" required>
                <option value="" hidden>Advisor</option>
                <?php 
                    // Prepare the SQL statement
                    $sql = "SELECT
                                EmployeeID,
                                EmployeeFirstName,
                                EmployeeLastName
                            FROM
                                Advisor
                            INNER JOIN
                                Employee
                            ON
                                Advisor.AdvisorID = Employee.EmployeeID";

                    if (!($stmt = $GLOBALS['db']->prepare($sql))) {
                        print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
                    }

                    // Execute the statement
                    if (!$stmt->execute()){
                        print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
                    }
                
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                ?>
                <option value="<?= $row["EmployeeID"] ?>"><?= $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] ?></option>
                <?php
                    }
                ?>
            </select><br />
            <input type="submit" name="editStudent" value="Save" />
        </form>
        <script type="text/javascript">select("#studentAdvisor", <?= $_GET["advisor"] ?>);</script>
        <?php
            if (isset($_POST["editStudent"])) {
                updateStudent($_GET["id"], $_POST["firstName"], $_POST["lastName"], $_POST["advisor"]);
            }
        ?>
    </div>
</div>
</body>