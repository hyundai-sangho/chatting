<?php

// POST로 이름 혹은 이메일 값이 안 넘어왔다면 회원가입 페이지로 이동시킴.
if (empty($_POST['name']) || empty($_POST['email'])) {
  header('location: ../index.php');
}

$name = $_POST["name"];
$email = $_POST["email"];


// 데이터베이스 클래스 추가
require_once '../db/Database.php';

// 디비 객체 생성
$database = new Database();
$resultRandomString = $database->getData_passwordFindTable_randomString();


// PHP MAILER 사용을 위한 autoload 추가
require_once '../vendor/autoload.php';

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable('../db/');
$dotenv->load();


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 세션 시작
session_start();
$_SESSION['authCode'] = $resultRandomString;


/* // 회원가입 시에 사용할 인증코드 쿠키에 저장 (쿠키 시간 5분)
setcookie('authCode', $resultRandomString, time() + 60 * 5, "/"); */

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
  //Server settings
  $mail->CharSet = "utf-8"; // 한글 깨짐 해결
  $mail->SMTPDebug = SMTP::DEBUG_SERVER;
  $mail->isSMTP();
  $mail->Host = 'smtp.naver.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'hyundai_sangho@naver.com';
  $mail->Password = $_ENV['EMAIL_PASSWORD'];
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port = 465;

  // 보내는 사람 이메일과, 이름
  $mail->setFrom('hyundai_sangho@naver.com', '조상호');

  // 받는 사람 이메일과, 이름
  $mail->addAddress("$email", "$name");

  //Content
  $mail->isHTML(true); //Set email format to HTML
  $mail->Subject = '채팅 회원 가입 이메일 인증 코드';
  $mail->Body = "$resultRandomString";
  $mail->AltBody = "$resultRandomString";

  $mail->send();
} catch (Exception $e) {

  echo "메시지를 보낼 수 없습니다. " . $mail->ErrorInfo . " " . $e->getMessage();

}



// 디비 커넥션 제거
$database->connectionClose();


?>
