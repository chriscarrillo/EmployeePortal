<!DOCTYPE html>
<html>
<head>
    <title>Employee Portal | Home</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css" />
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
            <a href="index.php" class="currentPage">Home</a>
            <a href="profile/">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <div id="content" class="clear">
        <form action="" method="POST" id="searchForm">
            <input type="text" name="searchText" id="searchText" placeholder="Search Students" autofocus />
            <select name="type">
                <option value="StudentID">ID</option>
                <option value="StudentFirstName">First Name</option>
                <option value="StudentLastName">Last Name</option>
                <option value="EmployeeFirstName">Advisor First Name</option>
                <option value="EmployeeLastName">Advisor Last Name</option>
            </select>
            <input type="submit" name="search" id="search" value="Search" />
        </form>
        <?php
            if (isset($_POST["search"])) {
                viewStudents(TRUE, $_POST["type"], $_POST["searchText"], "");
            } else {
                viewStudents();
            }
        ?>
    </div>
</div>
</body>
</html>