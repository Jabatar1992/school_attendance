<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";

// Required POST field
if (isset($_POST['student_id'])) {

    $student_id = cleanme(trim($_POST['student_id']));

    // ======================
    // VALIDATION
    // ======================
    if (input_is_invalid($student_id)) {
        respondBadRequest("Student ID is required.");
    } else if (!is_numeric($student_id)) {
        respondBadRequest("Student ID must be a number.");
    } else {

        // ======================
        // FETCH ATTENDANCE HISTORY
        // ======================
        $query = "
            SELECT id, student_id, class_id, attendance_date, status, created_at
            FROM attendances
            WHERE student_id = ?
            ORDER BY attendance_date DESC
        ";

        $stmt = $connect->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendanceHistory = [];

        while ($row = $result->fetch_assoc()) {
            $attendanceHistory[] = $row;
        }

        if (count($attendanceHistory) > 0) {
            respondOK($attendanceHistory, "Attendance history fetched successfully.");
        } else {
            respondBadRequest("No attendance records found for this student.");
        }
    }

} else {
    respondBadRequest("Invalid request. Required field student_id is missing.");
}

?>