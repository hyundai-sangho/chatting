<?php

require_once '../db/Database.php';

// login.php에서 POST로 받은 email 데이터가 있을 때만 쿠키 생성
if (isset($_POST['email'])) {
  // POST로 받은 email 데이터를 $email 변수에 저장
  $email = $_POST['email'];

  // 쿠키 생존 기간 30일
  setcookie('email', $email, time() + (86400 * 30), '/');
}

?>
