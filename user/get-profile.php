<?php


if (isset($_COOKIE['talkFriend'])) {

  include_once '../db/Database.php';

  // 디비 객체 생성
  $database = new Database();

  $userId = $_COOKIE['talkFriend'];

  // 디비에서 $userId 토대로 디비에서 데이터 가져오기
  $result = $database->getDataById($userId);

  echo "<a class='back-icon' href='users.php'>
          <i class='fas fa-arrow-left'></i>
        </a>
        <img src='$result[img]' alt='프로필 사진' />
        <div class='details'>
          <span>
            $result[name]
          </span>
          <p>
            $result[status]
          </p>
        </div>";

  // 디비 커넥션 끊기
  $database->connectionClose();
}



?>
