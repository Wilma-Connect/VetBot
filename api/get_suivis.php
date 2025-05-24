<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['user'])) {
  echo json_encode(["success" => false, "message" => "Non connecté"]);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM suivis WHERE user_id = ? ORDER BY remind_at ASC");
$stmt->execute([$_SESSION['user']['id']]);
$suivis = $stmt->fetchAll();
echo json_encode(["success" => true, "suivis" => $suivis]);
?>
