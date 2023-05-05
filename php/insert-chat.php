<?php

// 세션 사용
session_start();

if (isset($_SESSION['unique_id'])) {
  require_once '../db/Database.php';

  $database = new Database();

  $message = $_POST["message"];
  $outgoingId = $_POST["outgoing_id"];
  $incomingId = $_POST["incoming_id"];

  $database->getDataByMessageAndOutgoingIdAndIncomingId($message, $outgoingId, $incomingId);

  // 디비 커넥션 끊기
  $database->connectionClose();
} else {
  header("location: views/login.php");
}


?>
