<?php
session_start();
header('Content-Type: application/json');
echo json_encode([
  'referral_count' => isset($_SESSION['referral_count']) ? (int)$_SESSION['referral_count'] : 0
]);