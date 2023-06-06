<?php

// 데이터베이스 클래스 추가
require_once '../db/Database.php';
require_once '../vendor/autoload.php';

/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 로거 채널 생성
$log = new Logger('chatSite');

// log/info.log 파일에 로그 생성. 로그 레벨은 INFO
$log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

// signup.js에서 보내온 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);

// index.php 회원가입 화면에서 json 데이터로 보내온
// 이름, 이메일, 비밀번호, 이미지, 이메일 인증 코드 값을 변수에 집어넣는다.
$name = $json_array['name'] ?? null;
$email = $json_array['email'] ?? null;
$password = $json_array['password'] ?? null;
$imageFile = $json_array['compressedFile'] ?? null;
$authCode = $json_array['authCode'] ?? null;

// 이름, 이메일, 패스워드 중 하나라도 값이 안 들어왔다면
// 에러 문구 "모든 입력 필드는 필수입니다." 출력
if (empty($name) || empty($email) || empty($password)) {
  $log->info("{ message: '모든 입력 필드는 필수입니다.' location: 'user/signup.php' }");

  echo "모든 입력 필드는 필수입니다.";
  exit;
}

// 이메일 유효성 검사 시작
// 정규식에 안 맞는 유형의 이메일이 들어오면 바로
// 에러 출력 "올바른 이메일 주소를 입력해주세요."
if (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email) == false) {
  $log->info("{ message: '올바른 이메일 주소를 입력해주세요.' location: 'user/signup.php' }");

  echo "올바른 이메일 주소를 입력해주세요.";
  exit;
}

// 비밀번호 유효성 검사 시작
$pw = $password;
$num = preg_match('/[0-9]/u', $pw);
$eng = preg_match('/[a-z]/u', $pw);
$spe = preg_match("/[\!\@\#\$\%\^\&\*]/u", $pw);

if (strlen($pw) < 10 || strlen($pw) > 30) {
  $log->info("{ message: '비밀번호는 영문, 숫자, 특수문자를 혼합하여 최소 10자리 ~ 최대 30자리 이내로 입력해주세요.' location: 'user/signup.php' }");

  echo "비밀번호는 영문, 숫자, 특수문자를 혼합하여 최소 10자리 ~ 최대 30자리 이내로 입력해주세요.";
  exit;
}

if (preg_match("/\s/u", $pw) == true) {
  $log->info("{ message: '비밀번호는 공백 없이 입력해주세요.' location: 'user/signup.php' }");

  echo "비밀번호는 공백 없이 입력해주세요.";
  exit;
}

if ($num == 0 || $eng == 0 || $spe == 0) {
  $log->info("{ message: '영문, 숫자, 특수문자를 혼합하여 입력해주세요.' location: 'user/signup.php' }");

  echo "영문, 숫자, 특수문자를 혼합하여 입력해주세요.";
  exit;
}

// $name 변수의 공백 제거(이름에 띄어쓰기가 있을 경우 공백 제거)
$name = preg_replace("/\s+/", "", $name);


/**
 * 비밀번호 암복호화 함수
 * $action 매개변수 값을 e를 넣으면 암호화, d를 넣으면 복호화, 기본값은 암호화
 */
function password_crypt($string, $action = 'e') // $action 값은 기본값을 e(ncryted)로 한다.
{
  $secret_key = 'chosangho_secret_key';
  $secret_iv = 'chosangho_secret_iv';

  $output = false;
  $encrypt_method = "AES-256-CBC";
  $key = hash('sha256', $secret_key);
  $iv = substr(hash('sha256', $secret_iv), 0, 16);

  if ($action == 'e') { // e는 암호화
    $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));

  } else if ($action == 'd') { // d는 복호화
    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
  }

  return $output;
}

// 비밀번호 암복호화 함수에 비밀번호를 넣어서 암호화한 뒤 리턴 값으로 받아서 $encryptedPassword 변수에 저장
$encryptedPassword = password_crypt($password, 'e');

// 세션 사용
session_start();

// 인증코드 검사
// 세션 authCode가 없으면 에러 출력
if (empty($_SESSION['authCode'])) {
  // http_response_code(401);
  $log->info("{ message: '인증 코드 입력 시간이 3분 지났습니다.' location: 'user/signup.php' }");

  echo "인증 코드 입력 시간이 3분 지났습니다.";
  exit;
}
// 세션 authCode와 회원가입 화면에서 입력한 입력 코드 값이 다르다면 에러 출력
elseif ($authCode !== $_SESSION['authCode']) {
  // http_response_code(401);
  $log->info("{ message: '인증 코드가 다릅니다.' location: 'user/signup.php' }");

  echo "인증 코드가 다릅니다.";
  exit;
}

// 디비 객체 생성
$database = new Database();

echo $name . ' ' . $email . ' ' . $encryptedPassword . ' ' . $imageFile;
$database->signUp($name, $email, $encryptedPassword, $imageFile);

// 디비 커넥션 끊기
$database->connectionClose();
