<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$message = $data['message'] ?? '';
echo json_encode([
  "response" => "Conseil basé sur: " . htmlspecialchars($message)
]);
?>
