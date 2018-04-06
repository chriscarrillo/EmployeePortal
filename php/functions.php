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

    function viewStudents($search=FALSE, $type="StudentID", $searchText="", $table="") {
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
                    <th>Date Added</th>
                </tr>
                <tr>
    <?php
            while ($row = $result->fetch_assoc()) {
    ?>
                    <td><?= $row["StudentID"] ?></td>
                    <td><?= $row["StudentFirstName"] ?></td>
                    <td><?= $row["StudentLastName"] ?></td>
                    <td><?= $row["EmployeeFirstName"] . " " . $row["EmployeeLastName"] ?></td>
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
?>