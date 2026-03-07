<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";

// Required POST field
if (isset($_POST['attendance_id'])) {

    $attendance_id = cleanme(trim($_POST['attendance_id']));

    // ======================
    // VALIDATION
    // ======================
    if (input_is_invalid($attendance_id)) {
        respondBadRequest("Attendance ID is required.");
    } else if (!is_numeric($attendance_id)) {
        respondBadRequest("Attendance ID must be a number.");
    } else {

        // ======================
        // CHECK IF ATTENDANCE EXISTS
        // ======================
        $checkAttendance = $connect->prepare("
            SELECT id FROM attendances
            WHERE id = ?
        ");
        $checkAttendance->bind_param("i", $attendance_id);
        $checkAttendance->execute();
        $result = $checkAttendance->get_result();

        if ($result->num_rows == 0) {
            respondBadRequest("Attendance record not found.");
        } else {

            // ======================
            // DELETE ATTENDANCE
            // ======================
            $deleteAttendance = $connect->prepare("
                DELETE FROM attendances
                WHERE id = ?
            ");
            $deleteAttendance->bind_param("i", $attendance_id);
            $deleteAttendance->execute();

            if ($deleteAttendance->affected_rows > 0) {

                respondOK([], "Attendance deleted successfully.");

            } else {

                respondBadRequest("Failed to delete attendance.");

            }
        }
    }

} else {

    respondBadRequest("Invalid request. Attendance ID is required.");

}

?>