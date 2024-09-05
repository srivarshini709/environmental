<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

$response = '';

if (strpos(strtolower($message), 'appointment') !== false) {
    $response = 'You can view and manage your appointments on the "View Appointments" page.';
} elseif (strpos(strtolower($message), 'medication') !== false) {
    $response = 'You can check your medication schedule on the "Medication" page.';
} elseif (strpos(strtolower($message), 'record') !== false) {
    $response = 'You can view your medical records on the "Medical Records" page.';
} else {
    $response = 'Sorry, I did not understand that. Please try asking something else.';
}

echo json_encode(['response' => $response]);
?>
