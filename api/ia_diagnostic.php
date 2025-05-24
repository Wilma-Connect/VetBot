<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$message = $data['message'] ?? '';
echo json_encode([
  "response" => "Diagnostic basé sur: " . htmlspecialchars($message)
]);
?>
