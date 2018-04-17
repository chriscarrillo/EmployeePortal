<?php
    $db = new mysqli("127.0.0.1", "root", "password", "EmployeePortal");
    

    function isLoggedIn() {
        if (isset($_SESSION["username"])) {
            return true;
        } else {
            return false;
        }
    }

    function login($username, $password) {
        // Prepare the SQL statement
        if (!($stmt = $GLOBALS['db']->prepare("SELECT EmployeeID, EmployeeFirstName, EmployeeLastName, EmployeeUsername, EmployeePassword, EmployeeType, EmployeeDateAdded FROM Employee WHERE EmployeeUsername = ?"))){
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('s', $username)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        }
        
        $stmt->store_result(); // Store the result to get the properties
        $num_of_rows = $stmt->num_rows(); // Get the number of rows
        
        if ($num_of_rows == 0) {
            print "That username does not exist. Try again";
        } else {
            // Bind the result to variables
            $stmt->bind_result($id, $firstName, $lastName, $empUsername, $empPassword, $empType, $empDateAdded);
        
            while ($stmt->fetch()) {
                if (strcmp($password, $empPassword) !== 0) {
                    print "Incorrect password. Try again";
                } else {
                    $_SESSION["id"] = $id;
                    $_SESSION["firstName"] = $firstName;
                    $_SESSION["lastName"] = $lastName;
                    $_SESSION["username"] = $empUsername;
                    $_SESSION["type"] = $empType;
                    $_SESSION["dateAdded"] = $empDateAdded;
                    
                    $stmt->free_result(); // Free the results
                    $stmt->close(); // Close the statement
                    header("Location: .."); // Direct the user to the home page
                }
            }
        }
    }

    function getEmployeeType() {
        if ($_SESSION["type"] == 0) {
            return "Administrator";
        } else if ($_SESSION["type"] == 1) {
            return "Advisor";
        } else {
            return "Teacher";
        }
    }

    function updateGrade($gradeID, $studentID, $courseID) {
        // Prepare the SQL statement
        $sql = "UPDATE
                    StudentCourse
                SET
                    GradeID = ?
                WHERE
                    StudentID = ?
                AND
                    CourseID = ?";
        
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('iii', $gradeID, $studentID, $courseID)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        }
    }

    function updateProfile($id, $firstName, $lastName, $username, $password) {
        // Prepare the SQL statement
        $sql = "UPDATE Employee SET
                EmployeeFirstName = ?,
                EmployeeLastName = ?,
                EmployeeUsername = ?,
                EmployeePassword = ?
                WHERE EmployeeID = ?";
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('ssssi', $firstName, $lastName, $username, $password, $id)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        } else {
            updateSession($firstName, $lastName, $username);
            print "Saved!";
        }
    }

    function updateSession($firstName, $lastName, $username) {
        $_SESSION["firstName"] = $firstName;
        $_SESSION["lastName"] = $lastName;
        $_SESSION["username"] = $username;
    }

    function updateStudent($id, $firstName, $lastName, $advisor) {
        // Prepare the SQL statement
        $sql = "UPDATE Student SET
                StudentFirstName = ?,
                StudentLastName = ?,
                AdvisorID = ?
                WHERE StudentID = ?";
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('ssii', $firstName, $lastName, $advisor, $id)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        } else {
            print "Saved!";
        }
    }

    function updateCourse($id, $code, $name, $meeting, $teacher, $sectionID, $sectionLetter) {
        // Prepare the SQL statement
        $sql = "UPDATE Course SET
                CourseCode = ?,
                CourseName = ?,
                MeetingID = ?
                WHERE CourseID = ?";
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('ssii', $code, $name, $meeting, $id)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        }
        
        // Update the Section table
        $sectionSQL = "UPDATE Section SET
                       SectionLetter = ?,
                       TeacherID = ?
                       WHERE SectionID = ?";
        
        if (!($sectionStmt = $GLOBALS['db']->prepare($sectionSQL))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$sectionStmt->bind_param('sii', $sectionLetter, $teacher, $sectionID)){
            print "Binding parameters failed: (" . $sectionStmt->errno . ")" . $sectionStmt->error;
        }

        // Execute the statement
        if (!$sectionStmt->execute()) {
            print "Execute failed: (" . $sectionStmt->errno .")" . $sectionStmt->error;
        } else {
            print "Saved!";
        }
    }

    function viewStudents($search=FALSE, $type="Student.StudentFirstName", $searchText="") {
        if ($_SESSION["type"] == 0) {
            // Show all students
            $sql = "SELECT
                        Student.StudentID,
                        Student.StudentFirstName,
                        Student.StudentLastName,
                        Student.AdvisorID,
                        Student.StudentDateAdded,
                        Employee.EmployeeFirstName,
                        Employee.EmployeeLastName
                    FROM
                        Student
                    INNER JOIN
                        Employee
                    ON
                        Student.AdvisorID = Employee.EmployeeID
                    AND 
                        $type LIKE '%$searchText%'
                    ORDER BY $type";
        } else if ($_SESSION["type"] == 1) {
            // Show only students that the advisor advises
            $sql = "SELECT
                        Student.StudentID,
                        Student.StudentFirstName,
                        Student.StudentLastName,
                        Student.AdvisorID,
                        Student.StudentDateAdded,
                        Employee.EmployeeFirstName,
                        Employee.EmployeeLastName
                    FROM
                        Student
                    INNER JOIN
                        Employee
                    ON
                        Student.AdvisorID = Employee.EmployeeID
                    WHERE
                        Student.AdvisorID = '" . $_SESSION["id"] . "'
                    AND 
                        $type LIKE '%$searchText%'
                    ORDER BY $type";
        } else {
            // Show only the students the teacher teaches
            $sql = "SELECT
                        Section.SectionLetter,
                        Course.CourseID,
                        Course.CourseCode,
                        Student.StudentID,
                        Student.StudentFirstName,
                        Student.StudentLastName,
                        Student.StudentDateAdded,
                        Employee.EmployeeFirstName,
                        Employee.EmployeeLastName,
                        StudentCourse.GradeID 
                    FROM
                        Student,
                        Teacher,
                        Section,
                        Course,
                        StudentCourse,
                        Advisor,
                        Employee  
                    WHERE
                        Teacher.TeacherID = '" . $_SESSION["id"] . "'
                    AND
                        Section.TeacherID = Teacher.TeacherID 
                    AND 
                        Section.SectionID = Course.SectionID 
                    AND 
                        StudentCourse.CourseID = Course.CourseID
                    AND 
                        Student.StudentID = StudentCourse.StudentID
                    AND 
                        Student.AdvisorID = Advisor.AdvisorID
                    AND 
                        Advisor.AdvisorID = Employee.EmployeeID
                    AND 
                        $type LIKE '%$searchText%'
                    ORDER BY CourseCode";
        }
            if (!($stmt = $GLOBALS['db']->prepare($sql))) {
                print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
            }
            
            // Execute the statement
            if (!$stmt->execute()){
                print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
            }
            
            $result = $stmt->get_result();
    ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Advisor</th>
    <?php
                    if ($_SESSION["type"] == 2) {
                        $counter = 1;
    ?>
                        <th>Course</th>
                        <th>Grade</th>
    <?php
                    }
    ?>
                    <th>Date Added</th>
                </tr>
    <?php
            while ($row = $result->fetch_assoc()) {
    ?>
                <tr class="studentRow" data-href="student/index.php?id=<?= $row["StudentID"] ?>&firstName=<?= $row["StudentFirstName"] ?>&lastName=<?= $row["StudentLastName"] ?>&advisor=<?= $row["AdvisorID"] ?>" title="Edit Student (<?= $row["StudentFirstName"] . " " . $row["StudentLastName"] ?>)">
                    <td><?= $row["StudentID"] ?></td>
                    <td><?= $row["StudentFirstName"] ?></td>
                    <td><?= $row["StudentLastName"] ?></td>
                    <td><?= $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] ?></td>
    <?php
                    if ($_SESSION["type"] == 2) {
    ?>
                        <td><?= $row["CourseCode"] . "-" . $row["SectionLetter"] ?></td>
                        <td>
                            <form action="index.php?student=<?= $row["StudentID"] ?>&course=<?= $row["CourseID"] ?>" method="POST">
                                <select name="grade" id="grade<?= $counter ?>">
                                    <option value="1">A</option>
                                    <option value="2">A-</option>
                                    <option value="3">B+</option>
                                    <option value="4">B</option>
                                    <option value="5">B-</option>
                                    <option value="6">C+</option>
                                    <option value="7">C</option>
                                    <option value="8">C-</option>
                                    <option value="9">D+</option>
                                    <option value="10">D</option>
                                    <option value="11">D-</option>
                                    <option value="12">F</option>
                                </select>
                                <input type="submit" value="Update" name="updateGrade" />
                            </form>
                        </td>
                    <script type="text/javascript">selectGrade(document.getElementById("grade<?= $counter++ ?>"), <?= $row["GradeID"] ?>);</script>
    <?php
                    }
    ?>
                    <td>
    <?php
                        print date_format(date_create($row["StudentDateAdded"]), 'F j, Y g:i A');
    ?>
                    </td>
                </tr>
    <?php
            }
    ?>
            </table>
    <?php
    }

    function viewCourses($search=FALSE, $type="Course.CourseCode", $searchText="") {
        if ($_SESSION["type"] != 2) {
            $sql = "SELECT
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
                    WHERE
                        $type LIKE '%$searchText%'
                    ORDER BY $type";
        } else {
            $sql = "SELECT
                        Section.SectionID,
                        Section.SectionLetter,
                        Section.TeacherID,
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
                    WHERE
                        TeacherID = '" . $_SESSION["id"] . "'
                    AND
                        $type LIKE '%$searchText%'
                    ORDER BY $type";
        }
        
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        }

        $result = $stmt->get_result();
?>
        <table>
            <tr>
                <th>ID</th>
<?php
        if ($_SESSION["type"] != 2) {
?>
                <th>Teacher</th>
<?php
        }
?>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Course Meeting</th>
            </tr>
<?php
        while ($row = $result->fetch_assoc()) {
?>
            <tr class="courseRow" data-href="../course/index.php?id=<?= $row["CourseID"] ?>&code=<?= $row["CourseCode"] ?>&name=<?= $row["CourseName"] ?>&meeting=<?= $row["MeetingID"] ?>&teacher=<?= $row["EmployeeID"] ?>&sectionID=<?= $row["SectionID"] ?>&sectionLetter=<?= $row["SectionLetter"] ?>" title="Edit Course (<?= $row["CourseCode"] . " " . $row["CourseName"] ?>)">
                <td><?= $row["CourseID"] ?></td>
<?php
            if ($_SESSION["type"] != 2) {
?>
                <td><?= $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] ?></td>
<?php
            }
?>
                <td><?= $row["CourseCode"] . "-" . $row["SectionLetter"] ?></td>
                <td><?= $row["CourseName"] ?></td>
                <td><?php print $row["MeetingDay"] . "'s: " . date_format(date_create($row["MeetingStartTime"]), 'h:i A') . " - " . date_format(date_create($row["MeetingEndTime"]), 'h:i A') ?></td>
            </tr>
<?php
        }
?>
        </table>
    <?php
    }

    function createStudent($firstName, $lastName, $advisor) {
        // Prepare the SQL statement
        $sql = "INSERT INTO
                    Student
                    (
                        StudentFirstName,
                        StudentLastName,
                        AdvisorID
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?
                    )";
        if (!($stmt = $GLOBALS['db']->prepare($sql))) {
            print "Prepare failed: (" . $GLOBALS['db']->errno . ")" . $GLOBALS['db']->error;
        }
        
        // Bind the statement
        if (!$stmt->bind_param('ssi', $firstName, $lastName, $advisor)){
            print "Binding parameters failed: (" . $stmt->errno . ")" . $stmt->error;
        }

        // Execute the statement
        if (!$stmt->execute()){
            print "Execute failed: (" . $stmt->errno .")" . $stmt->error;
        } else {
            print "Student Created";
        }
    }

    function createCourse($code, $name, $teacher, $letter, $meeting) {
        // Prepare the SQL statement
        $sectionSql = "INSERT INTO
                    Section
                    (
                        SectionLetter,
                        TeacherID
                    )
                    VALUES
                    (
                        '$letter',
                        $teacher
                    )";
        
        $sectionQuery = $GLOBALS["db"]->query($sectionSql);
        
        if (!$sectionSql) {
            print $GLOBALS["db"]->error;
        } else {
            $sectionID = mysqli_insert_id($GLOBALS["db"]);

            $courseSql = "INSERT INTO
                        Course
                        (
                            CourseCode,
                            CourseName,
                            SectionID,
                            MeetingID
                        )
                        VALUES
                        (
                            '$code',
                            '$name',
                            $sectionID,
                            $meeting
                        )";

            $query = $GLOBALS["db"]->query($courseSql);

            if ($query) {
                print "Course Added";
            } else {
                print $GLOBALS["db"]->error;
            }
        }
    }
?>