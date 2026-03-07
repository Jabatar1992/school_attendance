<?php

$method = "GET";
$cache  = "no-cache";
include "../head.php";

// ======================
// FETCH ALL CLASSES
// ======================

$getClasses = $connect->prepare("
    SELECT id, class_name, class_teacher, created_at
    FROM classes
    ORDER BY id DESC
");

$getClasses->execute();
$result = $getClasses->get_result();

$classes = [];

while ($row = $result->fetch_assoc()) {

    $classes[] = $row;

}

if (count($classes) > 0) {

    respondOK($classes, "Classes fetched successfully");

} else {

    respondBadRequest("No classes found.");

}

?>