<?php

// 세션 사용
session_start();

// 로그인 or 회원가입 시에 생성된 unique_id가 존재할 때
if (isset($_SESSION['unique_id'])) {
  // POST 메소드로 리퀘스트 요청이 들어왔을 때만 실행
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $insertChat = json_decode(file_get_contents("php://input"), true);

    include_once '../db/Database.php';

    $database = new Database();

    $message = $insertChat["message"] ?? '';
    $outgoingId = $insertChat["outgoingId"];
    $incomingId = $insertChat["incomingId"];
    $imageFileName = $insertChat["image"] ?? '';

    $database->getDataByMessageAndOutgoingIdAndIncomingId($message, $outgoingId, $incomingId, $imageFileName);

    // 디비 커넥션 끊기
    $database->connectionClose();
  } else {
    header("location: ../index.php");
  }
} else {
  header("location: ../index.php");
}

?>
