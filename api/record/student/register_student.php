<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['first_name'], $_POST['last_name'], $_POST['admission_number'])) {

    $first_name       = cleanme(trim($_POST['first_name']));
    $last_name        = cleanme(trim($_POST['last_name']));
    $admission_number = cleanme(trim($_POST['admission_number']));

    $class_id = isset($_POST['class_id'])
                ? intval($_POST['class_id'])
                : null;

    // VALIDATION

    if (input_is_invalid($first_name) || input_is_invalid($last_name) || input_is_invalid($admission_number)) {

        respondBadRequest("First name, Last name and Admission number are required.");

    } else if (strlen($first_name) < 2) {

        respondBadRequest("First name must be at least 2 characters.");

    } else if (strlen($last_name) < 2) {

        respondBadRequest("Last name must be at least 2 characters.");

    } else if (!is_null($class_id) && !is_numeric($class_id)) {

        respondBadRequest("Class ID must be a number.");

    } else {

        // CHECK IF ADMISSION NUMBER EXISTS

        $checkStudent = $connect->prepare(
            "SELECT id FROM students WHERE admission_number = ?"
        );

        $checkStudent->bind_param("s", $admission_number);
        $checkStudent->execute();
        $result = $checkStudent->get_result();

        if ($result->num_rows > 0) {

            respondBadRequest("Student with this admission number already exists.");

        }

        // INSERT STUDENT

        $insertStudent = $connect->prepare("
            INSERT INTO students (first_name, last_name, admission_number, class_id)
            VALUES (?, ?, ?, ?)
        ");

        $insertStudent->bind_param(
            "sssi",
            $first_name,
            $last_name,
            $admission_number,
            $class_id
        );

        $insertStudent->execute();

        if ($insertStudent->affected_rows > 0) {

            $student_id = $connect->insert_id;

            $getStudent = $connect->prepare("
                SELECT id, first_name, last_name, admission_number, class_id
                FROM students
                WHERE id = ?
            ");

            $getStudent->bind_param("i", $student_id);
            $getStudent->execute();
            $studentDetails = $getStudent->get_result()->fetch_assoc();

            respondOK($studentDetails, "Student registered successfully");

        } else {

            respondBadRequest("Student registration failed.");
        }
    }

} else {

    respondBadRequest("Invalid request. Required fields missing.");
}

?>