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
        <?php
            if ($_SESSION["type"] == 0) {
        ?>
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
            }
        ?>
        
        <h2><?= $_GET["firstName"] . " " . $_GET["lastName"] . "'s " ?>Fees</h2>
        
        <?php
            viewFees($_GET["id"]);
            
            if ($_SESSION["type"] != 2) {
        ?>
        
        <a href="../createFee/index.php?id=<?= $_GET["id"] ?>">Add Fee</a>
        <?php
            }
        ?>
        
        <form action="" method="POST">
            <select name="feeToDelete">
                <option hidden>Select Fee to Delete...</option>
                <?php 
                    // Prepare the SQL statement
                    $feeSQL = "SELECT
                                    Fee.FeeID,
                                    Fee.FeeType,
                                    Fee.FeeAmount,
                                    Fee.FeeDueDate
                                FROM
                                    Fee
                                WHERE
                                    Fee.StudentID = ?";

                    if (!($stmt = $GLOBALS['db']->prepare($feeSQL))) {
                        print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
                    }
                
                    if (!$stmt->bind_param('i', $_GET["id"])){
                        print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
                    }

                    // Execute the statement
                    if (!$stmt->execute()){
                        print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
                    }
                
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                ?>
                <option value="<?= $row["FeeID"] ?>"><?= $row["FeeType"] . ": " . $row["FeeAmount"] ?></option>
                <?php
                    }
                ?>
            </select>
            <input type="submit" name="deleteFee" value="Delete Fee" />
        </form>
        
        <?php
            if (isset($_POST["deleteFee"])) {
                deleteFee($_POST["feeToDelete"]);
            }
        ?>
        
        <h2><?= $_GET["firstName"] . " " . $_GET["lastName"] . "'s " ?>Courses</h2>
        <?php
            viewCourses(FALSE, "Course.CourseCode", "", $_GET["id"]);
        ?>
        
        <form action="" method="POST">
            <select name="courseToAdd">
                <option hidden>Select Course to Add...</option>
                <?php 
                    // Prepare the SQL statement
                    $courseSQL = "SELECT
                                    Course.CourseID,
                                    Course.CourseCode,
                                    Course.CourseName,
                                    Section.SectionID,
                                    Section.SectionLetter,
                                    Employee.EmployeeID,
                                    Employee.EmployeeFirstName,
                                    Employee.EmployeeLastName,
                                    Meeting.MeetingID,
                                    Meeting.MeetingStartTime,
                                    Meeting.MeetingEndTime,
                                    Meeting.MeetingDay
                                FROM
                                    Course
                                INNER JOIN
                                    Section
                                ON
                                    Course.SectionID = Section.SectionID
                                INNER JOIN
                                    Employee
                                ON
                                    Section.TeacherID = Employee.EmployeeID
                                INNER JOIN
                                    Meeting
                                ON
                                    Course.MeetingID = Meeting.MeetingID
                                ORDER BY Course.CourseCode";

                    if (!($stmt = $GLOBALS['db']->prepare($courseSQL))) {
                        print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
                    }

                    // Execute the statement
                    if (!$stmt->execute()){
                        print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
                    }
                
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                ?>
                <option value="<?= $row["CourseID"] ?>"><?= $row["CourseCode"] . "-" . $row["SectionLetter"] . " " . $row["CourseName"] . ", " . $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] . ", Meets " ?><?php print $row["MeetingDay"] . "'s: " . date_format(date_create($row["MeetingStartTime"]), 'h:i A') . " - " . date_format(date_create($row["MeetingEndTime"]), 'h:i A') ?></option>
                <?php
                    }
                ?>
            </select>
            <input type="submit" name="addCourse" value="Add Course" />
        </form>
        <?php
            if (isset($_POST["addCourse"])) {
                addCourse($_GET["id"], $_POST["courseToAdd"]);
            }
        ?>
        
        <form action="" method="POST">
            <select name="courseToDelete">
                <option hidden>Select Course to Delete...</option>
                <?php 
                    // Prepare the SQL statement
                    $courseSQL = "SELECT
                                    Section.SectionID,
                                    Section.SectionLetter,
                                    Section.TeacherID,
                                    Employee.EmployeeFirstName,
                                    Employee.EmployeeLastName,
                                    Course.CourseID,
                                    Course.CourseCode,
                                    Course.CourseName,
                                    Meeting.MeetingID,
                                    Meeting.MeetingStartTime,
                                    Meeting.MeetingEndTime,
                                    Meeting.MeetingDay
                                FROM
                                    Section
                                INNER JOIN
                                    Course
                                ON
                                    Section.SectionID = Course.CourseID
                                INNER JOIN
                                    Meeting
                                ON
                                    Course.MeetingID = Meeting.MeetingID
                                INNER JOIN
                                    StudentCourse
                                ON
                                    Course.CourseID = StudentCourse.CourseID
                                INNER JOIN
                                    Employee
                                ON
                                    Employee.EmployeeID = Section.TeacherID
                                WHERE
                                    StudentCourse.StudentID = '" . $_GET["id"] . "'
                                ORDER BY Course.CourseCode";

                    if (!($stmt = $GLOBALS['db']->prepare($courseSQL))) {
                        print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
                    }

                    // Execute the statement
                    if (!$stmt->execute()){
                        print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
                    }
                
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                ?>
                <option value="<?= $row["CourseID"] ?>"><?= $row["CourseCode"] . "-" . $row["SectionLetter"] . " " . $row["CourseName"] . ", " . $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] . ", Meets " ?><?php print $row["MeetingDay"] . "'s: " . date_format(date_create($row["MeetingStartTime"]), 'h:i A') . " - " . date_format(date_create($row["MeetingEndTime"]), 'h:i A') ?></option>
                <?php
                    }
                ?>
            </select>
            <input type="submit" name="deleteCourse" value="Delete Course" />
        </form>
        
        <?php
            if (isset($_POST["deleteCourse"])) {
                deleteCourse($_GET["id"], $_POST["courseToDelete"]);
            }
        ?>
    </div>
</div>
</body>