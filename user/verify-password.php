<?php

// 세션 시작
session_start();

if (isset($_COOKIE) && isset($_SESSION)) {
  setcookie('name', '', time() - 3600, '/');
  setcookie('email', '', time() - 3600, '/');

  // 세션 변수 해제
  unset($_SESSION['authCode']);

  // 세션 파괴.
  session_destroy();

  echo "이름, 이메일 쿠키 삭제, 인증 코드 세션 삭제 성공";
  exit;
}

?>
