<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['admission_number'])) {

    $admission_number = cleanme(trim($_POST['admission_number']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($admission_number)) {

        respondBadRequest("Admission number is required.");

    } else {

        // ======================
        // FETCH STUDENT DETAILS
        // ======================

        $getStudent = $connect->prepare("
            SELECT id, first_name, last_name, admission_number, class_id
            FROM students
            WHERE admission_number = ?
        ");

        $getStudent->bind_param("s", $admission_number);
        $getStudent->execute();
        $result = $getStudent->get_result();

        if ($result->num_rows > 0) {

            $studentDetails = $result->fetch_assoc();
            respondOK($studentDetails, "Student details fetched successfully");

        } else {

            respondBadRequest("No student found with the given admission number.");
        }
    }

} else {

    respondBadRequest("Invalid request. Admission number is required.");
}

?>