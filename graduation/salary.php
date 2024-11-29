<?php
// Include database connection
require 'db1.php';

// Function to calculate salary for a specific teacher
function calculateSalary($teacher_id, $month, $year, $conn) {
    // Fetch teacher details (hourly rate) from the 'Teacher' table
    $teacherQuery = "SELECT name, hourly_rate FROM Teacher WHERE id = :teacher_id";
    $stmt = $conn->prepare($teacherQuery);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if teacher exists
    if (!$teacher) {
        echo "Teacher not found!";
        return;
    }

    // Fetch total working hours for the given month and year from the 'Lecture' table
    $hoursQuery = "
        SELECT SUM(comasuu) AS total_hours 
        FROM Lecture 
        WHERE teacher_id = :teacher_id 
        AND MONTH(date) = :month 
        AND YEAR(date) = :year";
    
    $stmt = $conn->prepare($hoursQuery);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Retrieve total hours worked
    $total_hours = $result['total_hours'] ?? 0;  // If no hours worked, default to 0
    $hourly_rate = $teacher['hourly_rate'];       // Get the hourly rate from the Teacher table
    $salary = $total_hours * $hourly_rate;        // Calculate total salary

    // Output the calculated salary
    echo "Salary Calculation for " . $teacher['name'] . ":<br>";
    echo "Total Hours Worked: " . $total_hours . " hours<br>";
    echo "Hourly Rate: $" . $hourly_rate . "<br>";
    echo "Total Salary: $" . $salary . "<br>";
}

// Example usage (for one specific teacher, in a specific month)
$teacher_id = 1; // Replace with the actual teacher ID
$month = 9; // For September
$year = 2024;
calculateSalary($teacher_id, $month, $year, $conn);
?>
