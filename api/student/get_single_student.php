<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";

if (isset($_POST['student_id'])) {

    $student_id = cleanme(trim($_POST['student_id']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($student_id)) {

        respondBadRequest("Student ID is required.");

    } else if (!is_numeric($student_id)) {

        respondBadRequest("Student ID must be a number.");

    } else {

        // ======================
        // FETCH STUDENT DETAILS
        // ======================

        $getStudent = $connect->prepare("
            SELECT id, first_name, last_name, admission_number, class_id
            FROM students
            WHERE id = ?
        ");

        $getStudent->bind_param("i", $student_id);
        $getStudent->execute();
        $result = $getStudent->get_result();

        if ($result->num_rows > 0) {

            $studentDetails = $result->fetch_assoc();
            respondOK($studentDetails, "Student details fetched successfully");

        } else {

            respondBadRequest("No student found with the given ID.");
        }
    }

} else {

    respondBadRequest("Invalid request. Required fields missing.");
}

?>