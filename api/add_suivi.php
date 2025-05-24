<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['user'])) {
  echo json_encode(["success" => false, "message" => "Non connecté"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'] ?? '';
$details = $data['details'] ?? '';
$remind_at = $data['remind_at'] ?? '';

if (!$title || !$remind_at) {
  echo json_encode(["success" => false, "message" => "Champs manquants"]);
  exit;
}

$stmt = $pdo->prepare("INSERT INTO suivis (user_id, title, details, remind_at) VALUES (?, ?, ?, ?)");
$stmt->execute([$_SESSION['user']['id'], $title, $details, $remind_at]);
echo json_encode(["success" => true]);
?>
