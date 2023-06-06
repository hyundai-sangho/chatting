<?php

// 세션 사용
// session_start();

require_once '../db/Database.php';

// chat.js에서 보내온 발신 id, 수신 id의 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);

$outgoingId = $json_array["outgoingId"];
$incomingId = $json_array["incomingId"];


// 디비 객체 생성
$database = new Database();
$database->getMessagesDataById($outgoingId, $incomingId);

// 디비 커넥션 끊기
$database->connectionClose();

?>
