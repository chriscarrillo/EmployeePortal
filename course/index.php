<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Course</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="../js/main.js"></script>
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
        <h2>Edit Course</h2>
        <form action="" method="POST" id="editCourse">
            <input type="text" name="code" value="<?= $_GET["code"] ?>" required /><br />
            <input type="text" name="name" value="<?= $_GET["name"] ?>" required /><br />
            <input type="text" name="sectionLetter" value="<?= $_GET["sectionLetter"] ?>" /><br />
            <select id="courseTeacher" name="teacher" required>
                <option value="" hidden>Teacher</option>
                <?php 
                    // Prepare the SQL statement
                    $sql = "SELECT
                                EmployeeID,
                                EmployeeFirstName,
                                EmployeeLastName
                            FROM
                                Teacher
                            INNER JOIN
                                Employee
                            ON
                                Teacher.TeacherID = Employee.EmployeeID";

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
                <option value="<?= $row["EmployeeID"] ?>"><?php print $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] ?></option>
                <?php
                    }
                ?>
            </select><br />
            <select id="courseMeeting" name="meeting" required>
                <option value="" hidden>Meeting Time</option>
                <?php 
                    // Prepare the SQL statement
                    $sql = "SELECT
                                MeetingID,
                                MeetingStartTime,
                                MeetingEndTime,
                                MeetingDay
                            FROM
                                Meeting
                            ORDER BY
                                MeetingDay";

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
                <option value="<?= $row["MeetingID"] ?>"><?php print $row["MeetingDay"] . "'s: " . date_format(date_create($row["MeetingStartTime"]), 'h:i A') . " - " . date_format(date_create($row["MeetingEndTime"]), 'h:i A') ?></option>
                <?php
                    }
                ?>
            </select><br />
            <input type="submit" name="editCourse" value="Save" />
        </form>
        <script type="text/javascript">
            select("#courseTeacher", <?= $_GET["teacher"] ?>);
            select("#courseMeeting", <?= $_GET["meeting"] ?>);
        </script>
        <?php
            if (isset($_POST["editCourse"])) {
                updateCourse($_GET["id"], $_POST["code"], $_POST["name"], $_POST["meeting"], $_POST["teacher"], $_GET["sectionID"], $_POST["sectionLetter"]);
            }
        ?>
    </div>
</div>
</body>