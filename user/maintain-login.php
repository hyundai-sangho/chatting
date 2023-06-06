<?php


require_once "../db/Database.php";
require_once "../vendor/autoload.php";


/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 로거 채널 생성
$log = new Logger('chatSite');

// log/info.log 파일에 로그 생성. 로그 레벨은 INFO
$log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

// login.js에서 보내온 이메일 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);

$email = $json_array['loginEmail'] ?? null;
$password = $json_array['loginPassword'] ?? null;


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

$database = new Database();
$result = $database->getUsersDataByEmailAndPassword($email, $encryptedPassword);

if (isset($result["email"]) && isset($result["password"])) {

  // 디비 커넥션 끊기
  $database->connectionClose();

  setcookie('email', $email, time() + (86400 * 3), '/'); // 쿠키 생존 기간 3일

  $log->info("{ message: '$email 이메일 쿠키 생성됨', location: 'user/maintain-login.php' }");

  exit;
} else {
  $log->info("{ message: '입력하신 이메일과 비밀번호를 다시 확인해 주세요.', location: 'user/maintain-login.php' }");

  echo ("입력하신 이메일과 비밀번호를 다시 확인해 주세요.");
  exit;
}


/* // login.php에서 로그인 상태 유지를 클릭해서
// POST로 받은 email 데이터가 있을 때만 쿠키 생성
if (isset($email)) {

  // 쿠키를 일부러 지우거나 하는 등의 행동을 하지 않는다면
  // 기존 쿠키 데이터를 브라우저에서 가지고 있다가 다시 로그인을 하려고 하면
  // 쿠키에 저장되어 있는 이메일 데이터를 바탕으로 디비와 연동해 해당 이메일과 일치하는 사용자를 찾고
  // 그런 사용자가 있다면 로그인 창에 이메일과 패스워드 값을 자동으로 채워주무로 따로 이메일과 패스워드를 입력하지 않아도
  // 로그인 버튼을 누르면 로그인이 가능함.
  setcookie('email', $email, time() + (86400 * 3), '/'); // 쿠키 생존 기간 3일
}
 */

?>
