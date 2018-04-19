<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Courses</title>
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
            <a href="" class="currentPage">Courses</a>
            <a href="../profile/">Profile</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="content" class="clear">
        <form action="" method="POST" id="searchForm">
            <input type="text" name="searchText" id="searchText" placeholder="Search Courses" autofocus />
            <select name="type">
                <option value="Course.CourseID">Course ID</option>
                <option value="Course.CourseCode">Course Code</option>
                <option value="Course.CourseName">Course Name</option>
            </select>
            <input type="submit" name="search" id="search" value="Search" />
        </form>
        <?php
            if (isset($_POST["search"])) {
                viewCourses(TRUE, $_POST["type"], $_POST["searchText"]);
            } else {
                viewCourses();
            }
        
            if ($_SESSION["type"] != 2) {
        ?>
            <a href="../createCourse">Add</a>
        <?php
            }
        
            if ($_SESSION["type"] == 0) {
        ?>
        <form action="" method="POST">
            <select name="courseToDelete">
                <option hidden>Select Course to Delete...</option>
                <?php 
                    // Prepare the SQL statement
                    $courseSQL = "SELECT
                                    Section.SectionID,
                                    Section.SectionLetter,
                                    Course.CourseID,
                                    Course.CourseCode,
                                    Course.CourseName
                                FROM
                                    Section
                                INNER JOIN
                                    Course
                                ON
                                    Section.SectionID = Course.CourseID
                                ORDER BY Course.CourseCode";

                    $query = $GLOBALS["db"]->query($courseSQL);
                
                    if (!$query) {
                        print $GLOBALS["db"]->error;
                    }
                
                    while ($row = mysqli_fetch_assoc($query)) {
                ?>
            <option value="<?= $row["CourseID"] ?>"><?= $row["CourseCode"] . "-" . $row["SectionLetter"] . " " . $row["CourseName"] ?></option>
                <?php
                    }
                ?>
            </select>
            <input type="submit" name="deleteCourse" value="Delete Course" />
        </form>
        <?php
            }
            if (isset($_POST["deleteCourse"])) {
                deleteCourseFromDatabase($_POST["courseToDelete"]);
            }
        ?>
    </div>
</div>
</body>