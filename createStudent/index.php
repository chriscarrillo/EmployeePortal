<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Create Student</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
    <script type="text/javascript" src="../js/main.js"></script>
</head>
<body>
<?php include "../php/functions.php"; ?>
<div id="home">
    <?php
        session_start();
        if (!isLoggedIn()) {
            header("Location: ../login");
        }
    ?>
    
    <header>
        <h1>Employee Portal | <?php print $_SESSION["firstName"] . " " . $_SESSION["lastName"] . " (" . getEmployeeType() . ")"; ?></h1>
        <nav>
            <a href="../">Home</a>
            <a href="../courses/">Courses</a>
            <a href="../profile/">Profile</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="content" class="clear">
        <h2>Create Student</h2>
        <form action="" method="POST" id="createStudent">
            <input type="text" name="firstName" placeholder="First Name" autofocus required /><br />
            <input type="text" name="lastName" placeholder="Last Name" required /><br />
            <select name="advisor" required>
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
            <input type="submit" name="createStudent" value="Create Student" />
        </form>
        <?php
            if (isset($_POST["createStudent"])) {
                createStudent($_POST["firstName"], $_POST["lastName"], $_POST["advisor"]);
            }
        ?>
    </div>
</div>
</body>