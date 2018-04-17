DROP DATABASE IF EXISTS EmployeePortal; -- Drop the database if it exists
CREATE DATABASE IF NOT EXISTS EmployeePortal; -- Create the EmployeePortal database
USE EmployeePortal; -- Make sure we are using the proper database

-- Create the Employee table
CREATE TABLE IF NOT EXISTS
	Employee
	(
		EmployeeID int NOT NULL AUTO_INCREMENT,
        EmployeeFirstName varchar(50) NOT NULL,
        EmployeeLastName varchar(50) NOT NULL,
        EmployeeUsername varchar(30) NOT NULL,
        EmployeePassword varchar(30) NOT NULL,
        EmployeeType int(1) NOT NULL,
        EmployeeDateAdded timestamp NOT NULL,
        CONSTRAINT Employee_PK PRIMARY KEY (EmployeeID),
        CONSTRAINT UC_Username UNIQUE (EmployeeUsername)
	);
    
-- Create the Administrator table
CREATE TABLE IF NOT EXISTS
	Administrator
    (
		AdministratorID int PRIMARY KEY,
        CONSTRAINT AdministratorID_FK FOREIGN KEY (AdministratorID) REFERENCES Employee(EmployeeID)
    );
    
-- Create the Advisor table
CREATE TABLE IF NOT EXISTS
	Advisor
    (
		AdvisorID int PRIMARY KEY,
        CONSTRAINT AdvisorID_FK FOREIGN KEY (AdvisorID) REFERENCES Employee(EmployeeID)
    );
    
-- Create the Teacher table
CREATE TABLE IF NOT EXISTS
	Teacher
    (
		TeacherID int PRIMARY KEY,
        CONSTRAINT TeacherID_FK FOREIGN KEY (TeacherID) REFERENCES Employee(EmployeeID)
    );
    
-- Create the Student table
CREATE TABLE IF NOT EXISTS
	Student
    (
		StudentID int NOT NULL AUTO_INCREMENT,
        StudentFirstName varchar(50) NOT NULL,
        StudentLastName varchar(50) NOT NULL,
        AdvisorID int NOT NULL,
        StudentDateAdded timestamp NOT NULL,
        CONSTRAINT Student_PK PRIMARY KEY (StudentID),
        CONSTRAINT StudentAdvisorID_FK FOREIGN KEY (AdvisorID) REFERENCES Advisor(AdvisorID)
    );
    
-- Create the Section table
CREATE TABLE IF NOT EXISTS
	Section
    (
		SectionID int NOT NULL AUTO_INCREMENT,
        SectionLetter char NOT NULL,
        TeacherID int NOT NULL,
        CONSTRAINT Section_PK PRIMARY KEY (SectionID),
        CONSTRAINT SectionTeacherID_FK FOREIGN KEY (TeacherID) REFERENCES Teacher(TeacherID)
    );
    
-- Create the Meeting table
CREATE TABLE IF NOT EXISTS
	Meeting
    (
		MeetingID int NOT NULL AUTO_INCREMENT,
        MeetingStartTime time NOT NULL,
        MeetingEndTime time NOT NULL,
        MeetingDay varchar(8) NOT NULL,
        CONSTRAINT Meeting_PK PRIMARY KEY (MeetingID)
    );

-- Create the Course table
CREATE TABLE IF NOT EXISTS
	Course
    (
		CourseID int NOT NULL AUTO_INCREMENT,
        CourseCode varchar(6) NOT NULL,
        CourseName varchar(50) NOT NULL,
        SectionID int NOT NULL,
        MeetingID int NOT NULL,
        CONSTRAINT Course_PK PRIMARY KEY (CourseID),
        CONSTRAINT MeetingID_FK FOREIGN KEY (MeetingID) REFERENCES Meeting(MeetingID)
    );

-- Create the Fee table
CREATE TABLE IF NOT EXISTS
	Fee
    (
		FeeID int NOT NULL AUTO_INCREMENT,
        FeeType varchar(20) NOT NULL,
        FeeAmount decimal NOT NULL,
        FeeDueDate datetime NOT NULL,
        StudentID int NOT NULL,
        CONSTRAINT Fee_PK PRIMARY KEY (FeeID),
        CONSTRAINT StudentID_FK FOREIGN KEY (StudentID) REFERENCES Student(StudentID)
    );

-- Create the Grade table
CREATE TABLE IF NOT EXISTS
	Grade
    (
		GradeID int NOT NULL AUTO_INCREMENT,
        GradeLetter varchar(2) NOT NULL,
        CONSTRAINT Grade_PK PRIMARY KEY (GradeID)
    );

-- Create StudentCourse table
CREATE TABLE IF NOT EXISTS
	StudentCourse
    (
		StudentID int NOT NULL,
        CourseID int NOT NULL,
        GradeID int DEFAULT 1,
        CONSTRAINT StudentCourse_PK PRIMARY KEY (StudentID, CourseID),
        CONSTRAINT GradeID_FK FOREIGN KEY (GradeID) REFERENCES Grade(GradeID)
    );
    
-- Create a trigger to insert EmployeeID into the corresponding Employee table type
USE EmployeePortal;

DELIMITER //

USE EmployeePortal//
CREATE TRIGGER after_employee_insert
	AFTER INSERT ON Employee
    FOR EACH ROW
    
BEGIN
    IF NEW.EmployeeType = 0 THEN
		INSERT INTO Administrator VALUES (NEW.EmployeeID);
	END IF;
    
	IF NEW.EmployeeType = 1 THEN
		INSERT INTO Advisor VALUES (NEW.EmployeeID);
	END IF;
    
    IF NEW.EmployeeType >= 2 OR NEW.EmployeeType < 0 THEN
		INSERT INTO Teacher VALUES (NEW.EmployeeID);
	END IF;
END//
DELIMITER ;

-- Create a trigger to delete and EmployeeID from Employee table when AdministratorID is deleted from Administrator table
USE EmployeePortal;

DELIMITER //

USE EmployeePortal//
CREATE TRIGGER after_admin_delete
	AFTER DELETE ON Administrator
    FOR EACH ROW
    
BEGIN
	DELETE FROM Employee WHERE EmployeeID = OLD.AdministratorID;
END//
DELIMITER ;

-- Create a trigger to delete and EmployeeID from Employee table when AdvisorID is deleted from Advisor table
USE EmployeePortal;

DELIMITER //

USE EmployeePortal//
CREATE TRIGGER after_advisor_delete
	AFTER DELETE ON Advisor
    FOR EACH ROW
    
BEGIN
	DELETE FROM Employee WHERE EmployeeID = OLD.AdvisorID;
END//
DELIMITER ;

-- Create a trigger to delete and EmployeeID from Employee table when TeacherID is deleted from Teacher table
USE EmployeePortal;

DELIMITER //

USE EmployeePortal//
CREATE TRIGGER after_teacher_delete
	AFTER DELETE ON Teacher
    FOR EACH ROW
    
BEGIN
	DELETE FROM Employee WHERE EmployeeID = OLD.TeacherID;
END//
DELIMITER ;