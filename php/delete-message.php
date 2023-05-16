<?php



// DELETE 메소드로 리퀘스트 요청이 들어왔을 때만 실행
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

  $deleteMessage = json_decode(file_get_contents("php://input"), true);

  // chat.js에서 POST로 받아온 deleteId
  $deleteId = $deleteMessage["deleteMessageId"];

  require_once '../db/Database.php';

  // 디비 객체 생성
  $database = new Database();

  $database->deleteMessage($deleteId);

  // 디비 커넥션 끊기
  $database->connectionClose();

}

// DELETE 메소드로 리퀘스트 요청을 보낸게 아니라 url을 직접 주소 표시줄에 입력해서 들어왔다면 홈 화면으로 이동시킴
else {
  header("Location: ../index.php");
}


?>
