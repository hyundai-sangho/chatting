<?php

session_start(); // 세션 사용

require_once '../db/Database.php';


// PUT 메소드로 리퀘스트 요청이 들어왔을 때만 실행
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
  $editProfileData = json_decode(file_get_contents("php://input"), true);

  $uniqueId = $_SESSION["unique_id"]; // 현재 로그인한 사용자 uniqueId
  $name = $editProfileData["name"]; // 프로필 수정 화면에서 POST로 넘어온 이름
  $email = $editProfileData["email"]; // 프로필 수정 화면에서 POST로 넘어온 이메일
  $password = $editProfileData["password"]; // 프로필 수정 화면에서 POST로 넘어온 패스워드
  $image = $editProfileData["image"] ?? null; // 프로필 수정 화면에서 파일 선택 사진 변경을 통해 넘어온 데이터


  // $name 변수의 공백 제거(이름에 띄어쓰기가 있을 경우 공백 제거)
  $name = preg_replace("/\s+/", "", $name);

  // 디비 객체 생성
  $database = new Database();

  $result = $database->getDataById($uniqueId);




  // ===================================================================================
// 이메일 검사 시작
  if ($email !== $result['email']) {
    // 이메일 유효성 검사 시작
    // 정규식에 안 맞는 유형의 이메일이 들어오면 바로
    // 에러 출력 "올바른 이메일 주소를 입력해주세요."
    if (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email) == false) {
      echo "올바른 이메일 주소를 입력해주세요.";
      exit;
    }
  }
  // 이메일 검사 끝
// ===================================================================================

  // ===================================================================================
// 비밀번호 검사
// 프로필 수정 화면에서 넘어온 비밀번호 값과 세션 uniqueId로 디비에서 가져온 비밀번호 데이터가 같다면
// 유효성 검사를 할 필요가 없어짐.
  if ($password !== $result['password']) {

    // 비밀번호 유효성 검사 시작
    $pw = $password;
    $num = preg_match('/[0-9]/u', $pw);
    $eng = preg_match('/[a-z]/u', $pw);
    $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u", $pw);

    if (strlen($pw) < 10 || strlen($pw) > 30) {
      echo "비밀번호는 영문, 숫자, 특수문자를 혼합하여 최소 10자리 ~ 최대 30자리 이내로 입력해주세요.";
      exit;
    } elseif (preg_match("/\s/u", $pw) == true) {
      echo "비밀번호는 공백 없이 입력해주세요.";
      exit;
    } elseif ($num == 0 || $eng == 0 || $spe == 0) {
      echo "영문, 숫자, 특수문자를 혼합하여 입력해주세요.";
      exit;
    }

    /**
     * 비밀번호 암복호화 함수
     */
    function password_crypt($string, $action = 'e') // $action 값은 기본값을 e(ncryted)로 한다.
    {
      $secret_key = 'chosangho_secret_key';
      $secret_iv = 'chosangho_secret_iv';

      $output = false;
      $encrypt_method = "AES-256-CBC";
      $key = hash('sha256', $secret_key);
      $iv = substr(hash('sha256', $secret_iv), 0, 16);

      if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
      } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
      }

      return $output;
    }

    $password = password_crypt($password, 'e');
  }

  // 비밀번호 검사 끝
// ===================================================================================


  // ===================================================================================
// 이미지 검사 시작
  if ($image !== null) { // 프로필 수정 화면에서 파일 선택으로 넘어온 데이터가 있다면
    $sendImage = $image; // sendImage 변수에 $image 값 저장
  } else {
    $sendImage = null;
  }
  // 이미지 검사 끝
// ===================================================================================



  // 데이터 수정하기
  $database->editUserData($name, $email, $password, $sendImage, $uniqueId);

  // 디비 커넥션 끊기
  $database->connectionClose();
}

// 프로필 수정을 정상적인 방법을 통해 들어온 것이 아니라 직접 주소 표시줄에 url를 치고 들어왔다면 홈 화면으로 이동시킴
else {
  header('location: ../index.php');
}



?>
