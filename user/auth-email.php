<?php

// PHP MAILER, monolog 사용을 위한 autoload 추가
require_once '../vendor/autoload.php';

// 데이터베이스 클래스 추가
require_once '../db/Database.php';

/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// PHPMailer 클래스를 전역 네임스페이스로 가져오기
// 함수 내부가 아니라 스크립트 상단에 있어야 합니다.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // 로거 채널 생성
  $log = new Logger('chatSite');

  // log/info.log 파일에 로그 생성. 로그 레벨은 INFO
  $log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

  // signup.js에서 보내온 이름과, 이메일 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
  $json_array = json_decode(file_get_contents('php://input'), true);

  $name = $json_array['name'] ?? null;
  $email = $json_array['email'] ?? null;

  // 이름 혹은 이메일 값이 안 넘어왔다면 회원가입 페이지로 이동시킴.
  if (empty($name) || empty($email)) {
    $log->info("{ message: '이름 혹은 이메일이 존재하지 않습니다.' location: 'user/auth-email.php' }");

    header('location: ../index.php');
  }


  // 디비 객체 생성
  $database = new Database();
  $resultRandomString = $database->getData_passwordFindTable_randomString();

  // 디비 커넥션 제거
  $database->connectionClose();

  /* php dotenv 사용법 */
  $dotenv = Dotenv\Dotenv::createImmutable('../db/');
  $dotenv->load();

  // 세션 시작
  session_start();
  $_SESSION['authCode'] = $resultRandomString;

  // 인스턴스를 생성합니다. 'true'를 전달하면 예외가 활성화됩니다.
  $mail = new PHPMailer(true);

  try {

    // 서버 설정
    $mail->CharSet = "utf-8"; // 한글 깨짐 해결
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = $_ENV["EMAIL_HOST"];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['EMAIL_USERNAME'];
    $mail->Password = $_ENV['EMAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // 보내는 사람 이메일과, 이름
    $mail->setFrom($_ENV['EMAIL_USERNAME'], $_ENV["NAME"]);

    // 받는 사람 이메일과, 이름
    $mail->addAddress("$email", "$name");

    // 콘텐츠
    $mail->isHTML(true); // 이메일 형식을 HTML로 설정
    $mail->Subject = '채팅 회원 가입 이메일 인증 코드';
    $mail->Body = "$resultRandomString";
    $mail->AltBody = "$resultRandomString";

    $mail->send();

    $log->info("{ code: '200', message: '이메일 전송 완료.', location: 'user/auth-email.php' }");

    http_response_code(200);
    echo "{ code: '200', message: '이메일 전송 완료.', location: 'user/auth-email.php' }";
    exit;

  } catch (Exception $error) {

    $log->info("{ message: '메시지를 보낼 수 없습니다.' " . $mail->ErrorInfo . " " . $error->getMessage() . " " . "location: 'user/auth-email.php'}");

    echo "메시지를 보낼 수 없습니다." . $mail->ErrorInfo . " " . $error->getMessage();
    exit;
  }

} else {

  $log->info("{ message: 'POST 요청이 아닙니다.' location: 'user/auth-email.php'}");

  echo "POST 요청이 아닙니다.";
  exit;
}



?>
