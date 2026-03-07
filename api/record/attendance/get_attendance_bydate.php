<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

// Required POST fields
if (isset($_POST['class_id'], $_POST['start_date'], $_POST['end_date'])) {

    $class_id   = cleanme(trim($_POST['class_id']));
    $start_date = cleanme(trim($_POST['start_date']));
    $end_date   = cleanme(trim($_POST['end_date']));

    // ======================
    // VALIDATION
    // ======================
    if (input_is_invalid($class_id) || input_is_invalid($start_date) || input_is_invalid($end_date)) {
        respondBadRequest("Class ID, Start Date and End Date are required.");
    } else if (!is_numeric($class_id)) {
        respondBadRequest("Class ID must be a number.");
    } else if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date) ||
               !preg_match("/^\d{4}-\d{2}-\d{2}$/", $end_date)) {
        respondBadRequest("Dates must be in YYYY-MM-DD format.");
    } else if ($start_date > $end_date) {
        respondBadRequest("Start date cannot be after end date.");
    } else {

        // ======================
        // FETCH ATTENDANCE FOR RANGE
        // ======================
        $query = "
            SELECT a.id, a.student_id, s.first_name, s.last_name, 
                   a.class_id, a.attendance_date, a.status, a.created_at
            FROM attendances a
            LEFT JOIN students s ON a.student_id = s.id
            WHERE a.class_id = ? 
              AND a.attendance_date BETWEEN ? AND ?
            ORDER BY a.attendance_date ASC, s.last_name, s.first_name
        ";

        $stmt = $connect->prepare($query);
        $stmt->bind_param("iss", $class_id, $start_date, $end_date);
        $stmt->execute();

        $result = $stmt->get_result();
        $attendances = [];

        while ($row = $result->fetch_assoc()) {
            $attendances[] = $row;
        }

        if (count($attendances) > 0) {
            respondOK($attendances, "Attendance fetched successfully for the selected range.");
        } else {
            respondOK([], "No attendance records found for this class in the selected range.");
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>