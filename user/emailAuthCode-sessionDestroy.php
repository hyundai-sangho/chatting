<?php

require_once '../vendor/autoload.php';

/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 로거 채널 생성
$log = new Logger('chatSite');

// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

// 세션 사용
session_start();

$_SESSION['authCode'] = $_SESSION['authCode'] ?? null;

// 세션에 이메일 인증 코드가 있을 때
if (isset($_SESSION['authCode'])) {

  if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // 세션 authCode 변수 삭제
    unset($_SESSION['authCode']);

    // 세션 파일 삭제
    session_destroy();

    http_response_code(response_code: 200);
    $log->info("{ code: '200', message: '이메일 인증 코드가 성공적으로 삭제되었습니다.', location: 'user/emailAuthCode-sessionDestroy.php' }");

    http_response_code(200);
    echo "이메일 인증 코드가 성공적으로 삭제되었습니다.";
    exit;
  } else {
    $log->info("{ message: 'DELETE 요청이 아닙니다.', location: 'user/emailAuthCode-sessionDestroy.php' }");

    echo "DELETE 요청이 아닙니다.";
    exit;
  }

} else {
  $log->info("{ message: '이메일 인증 코드가 존재하지 않아 삭제할 수 없습니다.', location: 'user/emailAuthCode-sessionDestroy.php' }");

  echo "이메일 인증 코드가 존재하지 않아 삭제할 수 없습니다.";
  exit;
}

?>
