<?php

// 데이터베이스 클래스 추가
require_once '../db/Database.php';

// PHP MAILER 사용을 위한 autoload 추가
require_once '../vendor/autoload.php';

// PHPMailer 클래스를 전역 네임 스페이스로 가져오기
// 함수 내부가 아니라 스크립트 상단에 있어야 합니다.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// find-password.js에서 보내온 이름, 이메일 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);

$name = $json_array["findPasswordName"];
$email = $json_array["findPasswordEmail"];

// 디비 객체 생성
$database = new Database();
$result = $database->getDataByEmail($email);
$resultEncryptedPassword = $result['password'];
$resultRandomNumber = $database->getPasswordRandomAuthCode();


/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable('../db/');
$dotenv->load();


if ($resultEncryptedPassword) {

  // auth-code.php 에서 사용할 이메일을 쿠키에 저장
  setcookie('email', $email, time() + 60 * 5, "/");

  // 세션 시작
  session_start();
  // auth-code.php 에서 사용할 인증 코드를 세션에 저장
  $_SESSION['authCode'] = $resultRandomNumber;


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
    $mail->Subject = '채팅 회원 비밀번호 찾기 인증 코드';
    $mail->Body = "$resultRandomNumber";
    $mail->AltBody = "$resultRandomNumber";

    $mail->send();


  } catch (Exception $e) {
    echo "메시지를 보낼 수 없습니다.";
    // echo "메시지를 보낼 수 없습니다. 메일러 오류: {$mail->ErrorInfo}";

  }

} else {

  echo "입력하신 이름과 이메일을 다시 확인해 주세요.";

}


// 디비 커넥션 제거
$database->connectionClose();

?>
