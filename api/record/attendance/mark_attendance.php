<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

// Required POST fields
if (isset($_POST['student_id'], $_POST['class_id'], $_POST['attendance_date'], $_POST['status'])) {

    $student_id      = cleanme(trim($_POST['student_id']));
    $class_id        = cleanme(trim($_POST['class_id']));
    $attendance_date = cleanme(trim($_POST['attendance_date']));
    $status          = cleanme(trim($_POST['status']));

    // ======================
    // VALIDATION
    // ======================
    if (
        input_is_invalid($student_id) ||
        input_is_invalid($class_id) ||
        input_is_invalid($attendance_date) ||
        input_is_invalid($status)
    ) {
        respondBadRequest("All fields are required.");
    } else if (!is_numeric($student_id)) {
        respondBadRequest("Student ID must be a number.");
    } else if (!is_numeric($class_id)) {
        respondBadRequest("Class ID must be a number.");
    } else if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $attendance_date)) {
        respondBadRequest("Attendance date must be in YYYY-MM-DD format.");
    } else if (!in_array($status, ['Present', 'Absent', 'Late'])) {
        respondBadRequest("Invalid attendance status.");
    } else {

        // ======================
        // CHECK IF ATTENDANCE ALREADY EXISTS
        // ======================
       
    $checkAttendance = $connect->prepare("
    
        SELECT id FROM attendances
        WHERE student_id = ? AND class_id = ? AND attendance_date = ?
    ");
    $checkAttendance->bind_param("iis", $student_id, $class_id, $attendance_date);
    $checkAttendance->execute();
    $result = $checkAttendance->get_result();

    if ($result->num_rows > 0) {
        respondBadRequest("Attendance already marked for this student on this date.");
    } else {
        // safe to insert
    }






        $checkAttendance->bind_param("iis", $student_id, $class_id, $attendance_date);
        $checkAttendance->execute();
        $result = $checkAttendance->get_result();

        if ($result->num_rows > 0) {
            respondBadRequest("Attendance already marked for this student on this date.");
        } else {

            // ======================
            // INSERT ATTENDANCE
            // ======================
            $insertAttendance = $connect->prepare("
                INSERT INTO attendances (student_id, class_id, attendance_date, status)
                VALUES (?, ?, ?, ?)
            ");
            $insertAttendance->bind_param("iiss", $student_id, $class_id, $attendance_date, $status);
            $insertAttendance->execute();

            if ($insertAttendance->affected_rows > 0) {

                $attendance_id = $connect->insert_id;

                // Fetch inserted record
                $getAttendance = $connect->prepare("
                    SELECT id, student_id, class_id, attendance_date, status, created_at
                    FROM attendances
                    WHERE id = ?
                ");
                $getAttendance->bind_param("i", $attendance_id);
                $getAttendance->execute();
                $attendanceDetails = $getAttendance->get_result()->fetch_assoc();

                respondOK($attendanceDetails, "Attendance marked successfully.");
            } else {
                respondBadRequest("Failed to mark attendance.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>