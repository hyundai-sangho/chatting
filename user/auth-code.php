<?php

require_once '../vendor/autoload.php';

/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 로거 채널 생성
$log = new Logger('chatSite');

// log/info.log 파일에 로그 생성. 로그 레벨은 INFO
$log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

// auth-code.js에서 보내온 이메일 인증 코드 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);
$emailAuthCode = $json_array['emailAuthCode'];


// 세션 시작
session_start();

// 세션에 authCode가 없는 상태에서 임의로 사용자가 auth-code.php url을
// 주소표시줄에 입력해서 들어오면 이전 페이지가 있으면 이전 페이지로 보내고
// 이전 페이지가 없으면 login.php로 보내버림
if (empty($_SESSION['authCode'])) {

  // 변수에 이전 페이지 정보를 저장
  $prevPage = $_SERVER['HTTP_REFERER'] ?? '../login.php';

  header('location:' . $prevPage);
}

// 세션에 등록된 authCode와 인증코드 화면에서 사용자가 입력한 인증코드가 같으면
if ($_SESSION['authCode'] !== $emailAuthCode) {
  $log->info("{ message: '이메일에서 받은 인증 코드와 다릅니다.', location: 'user/auth-code.php' }");

  echo "이메일에서 받은 인증 코드와 다릅니다.";
  exit;

} else {
  $log->info("{ message: '비밀번호 찾기 인증 성공', location: 'user/auth-code.php' }");

  http_response_code(200);
  echo "비밀번호 찾기 인증 성공";
  exit;
}

?>
