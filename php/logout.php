<?php

// 세션 사용
session_start();

if (isset($_SESSION['unique_id'])) {
  require_once '../db/Database.php';

  $database = new Database();

  $result = $database->logout($_SESSION['unique_id']);

  if ($result == '업데이트 성공') {
    // 세션 unique_id 값을 지워버림으로써 로그아웃이 된 상태가 됨.
    unset($_SESSION['unique_id']);

    // 세션 자체를 제거
    session_destroy();

    // 쿠키에 저장되어 있는 email 제거
    setcookie('email', '', time() - 3600, '/');

    // 디비 커넥션 끊기
    $database->connectionClose();

    // 로그아웃이 되버렸기에 로그인 페이지로 이동시킴.
    header("Location: ../login.php");
  }
} else {
  // 세션이 없는 상태에서 로그아웃 페이지에 들어왔다면 로그인 페이지로 이동시킴.
  header("Location: ../login.php");
}



?>
