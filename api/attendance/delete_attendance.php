<?php
include "../head.php";

// Example admin key (you should store securely in DB or env)
define('ADMIN_KEY', 'supersecretadmin123');

if (isset($_POST['student_id'], $_POST['date'], $_POST['admin_key'])) {

    $student_id = cleanme($_POST['student_id']);
    $date       = cleanme($_POST['date']);
    $admin_key  = cleanme($_POST['admin_key']);

    // Check admin authorization
    if ($admin_key !== ADMIN_KEY) {
        respondBadRequest("Unauthorized: Admin access required");
        exit;
    }

    // Delete attendance
    $stmt = $conn->prepare("DELETE FROM attendance WHERE student_id=? AND date=?");
    $stmt->bind_param("is", $student_id, $date);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            respondOK("Attendance record deleted successfully");
        } else {
            respondBadRequest("No attendance record found for this student on this date");
        }
    } else {
        respondServerError("Error deleting attendance record");
    }

} else {
    respondBadRequest("Missing required parameters");
}
?>