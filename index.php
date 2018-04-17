<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Home</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</head>
<body>
<?php include "php/functions.php"; ?>
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
            <a href="" class="currentPage">Home</a>
            <a href="courses/">Courses</a>
            <a href="profile/">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="content" class="clear">
        <form action="" method="POST" id="searchForm">
            <input type="text" name="searchText" id="searchText" placeholder="Search Students" autofocus />
            <select name="type">
                <option value="Student.StudentID">Student ID</option>
                <option value="Student.StudentFirstName">First Name</option>
                <option value="Student.StudentLastName">Last Name</option>
                <option value="Employee.EmployeeFirstName">Advisor First Name</option>
                <option value="Employee.EmployeeLastName">Advisor Last Name</option>
            </select>
            <input type="submit" name="search" id="search" value="Search" />
        </form>
        <?php
            if (isset($_POST["search"])) {
                viewStudents(TRUE, $_POST["type"], $_POST["searchText"]);
            } else {
                viewStudents();
            }
        
            if (isset($_POST["updateGrade"])) {
                updateGrade($_POST["grade"], $_GET["student"], $_GET["course"]);
                unset($_POST["updateGrade"]);
            }
        
            if ($_SESSION["type"] != 2) {
        ?>
        <a href="createStudent">Add</a>
        <?php
            }
        ?>
    </div>
</div>
</body>
</html>