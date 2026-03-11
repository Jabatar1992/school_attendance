<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['class_name'])) {

    $class_name    = cleanme(trim($_POST['class_name']));
    $class_teacher = isset($_POST['class_teacher'])
                     ? cleanme(trim($_POST['class_teacher']))
                     : null;

    
    // VALIDATION
    

    if (input_is_invalid($class_name)) {

        respondBadRequest("Class name is required.");

    } else if (strlen($class_name) < 2) {

        respondBadRequest("Class name must be at least 2 characters.");

    } else {

        // CHECK IF CLASS EXISTS

        $checkClass = $connect->prepare(
            "SELECT id FROM classes WHERE class_name = ?"
        );

        $checkClass->bind_param("s", $class_name);
        $checkClass->execute();
        $result = $checkClass->get_result();

        if ($result->num_rows > 0) {

            respondBadRequest("Class already exists.");

        }

        // INSERT CLASS

        $insertClass = $connect->prepare("
            INSERT INTO classes (class_name, class_teacher)
            VALUES (?, ?)
        ");

        $insertClass->bind_param(
            "ss",
            $class_name,
            $class_teacher
        );

        $insertClass->execute();

        if ($insertClass->affected_rows > 0) {

            $class_id = $connect->insert_id;

            $getClass = $connect->prepare("
                SELECT id, class_name, class_teacher, created_at
                FROM classes
                WHERE id = ?
            ");

            $getClass->bind_param("i", $class_id);
            $getClass->execute();
            $classDetails = $getClass->get_result()->fetch_assoc();

            respondOK($classDetails, "Class created successfully");

        } else {

            respondBadRequest("Class creation failed.");
        }
    }

} else {

    respondBadRequest("Invalid request. Class name is required.");
}

?>