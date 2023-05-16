<?php


// 쿠키에 authCode가 없는 상태에서 임의로 사용자가 url을 주소표시줄에 입력해서 들어오면
// login.php로 보내버림
if (empty($_COOKIE['authCode'])) {
  // 변수에 이전 페이지 정보를 저장
  $prevPage = $_SERVER['HTTP_REFERER'] ?? '../login.php';

  header('location:' . $prevPage);
}

// 쿠키에 등록된 authCode와 인증코드 화면에서 사용자가 입력한 인증코드가 같으면
if ($_COOKIE['authCode'] !== $_POST['auth-code']) {
  echo nl2br("이메일에서 받은 인증 코드와 다릅니다.");
}

?>
