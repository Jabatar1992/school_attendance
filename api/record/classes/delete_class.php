<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['class_id'])) {

    $class_id = cleanme(trim($_POST['class_id']));

   
    // VALIDATION
   

    if (input_is_invalid($class_id)) {

        respondBadRequest("Class ID is required.");

    } else if (!is_numeric($class_id)) {

        respondBadRequest("Class ID must be a number.");

    } else {

       
        // CHECK IF CLASS EXISTS
       

        $checkClass = $connect->prepare(
            "SELECT id, class_name FROM classes WHERE id = ?"
        );

        $checkClass->bind_param("i", $class_id);
        $checkClass->execute();
        $result = $checkClass->get_result();

        if ($result->num_rows == 0) {

            respondBadRequest("Class not found.");

        }

        $class = $result->fetch_assoc();

        
        // DELETE CLASS
      

        $deleteClass = $connect->prepare(
            "DELETE FROM classes WHERE id = ?"
        );

        $deleteClass->bind_param("i", $class_id);
        $deleteClass->execute();

        if ($deleteClass->affected_rows > 0) {

            respondOK($class, "Class deleted successfully.");

        } else {

            respondBadRequest("Failed to delete class.");
        }
    }

} else {

    respondBadRequest("Invalid request. Class ID is required.");
}

?>