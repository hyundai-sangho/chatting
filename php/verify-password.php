<?php

if (isset($_COOKIE)) {
  setcookie('name', '', time() - 3600, '/');
  setcookie('email', '', time() - 3600, '/');
  setcookie('authCode', '', time() - 3600, '/');

  echo "이름, 이메일, 인증 코드 쿠키 삭제 성공";
}

?>
