<?php

// // 세션 사용
// session_start();

require_once '../db/Database.php';

$outgoingId = $_POST["outgoing_id"];
$incomingId = $_POST["incoming_id"];


// 디비 객체 생성
$database = new Database();
$database->getMessagesDataById($outgoingId, $incomingId);

// 디비 커넥션 끊기
$database->connectionClose();

?>
