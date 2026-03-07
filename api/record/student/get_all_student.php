<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['class_id'])) {

    $class_id = cleanme(trim($_POST['class_id']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($class_id)) {

        respondBadRequest("Class ID is required.");

    } else if (!is_numeric($class_id)) {

        respondBadRequest("Class ID must be a number.");

    } else {

        // ======================
        // FETCH STUDENTS IN CLASS
        // ======================

        $getStudents = $connect->prepare("
            SELECT id, first_name, last_name, admission_number, class_id
            FROM students
            WHERE class_id = ?
            ORDER BY last_name, first_name
        ");

        $getStudents->bind_param("i", $class_id);
        $getStudents->execute();
        $result = $getStudents->get_result();

        if ($result->num_rows > 0) {

            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }

            respondOK($students, "Students fetched successfully");

        } else {

            respondBadRequest("No students found in this class.");
        }
    }

} else {

    respondBadRequest("Invalid request. Required fields missing.");
}

?>c