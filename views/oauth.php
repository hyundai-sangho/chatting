<?php

try {

  // ![수정필요] 카카오 API 환경설정 파일
  include_once "../config.php";

  include_once "../db/Database.php";

  // 기본 응답 설정
  $res = array('rst' => 'fail', 'code' => (__LINE__ * -1), 'msg' => '');

  $code = $_GET['code'] ?? null;
  $state = $_GET['state'] ?? null;
  $cookieState = $_COOKIE['state'] ?? null;


  // code && state 체크
  if (empty($code) || empty($state) || $state != $cookieState) {
    // throw new Exception("인증실패", (__LINE__ * -1));
    echo new Exception("인증실패", (__LINE__ * -1));
  }

  // 토큰 요청
  $replace = array(
    '{grant_type}' => 'authorization_code',
    '{client_id}' => $kakaoConfig['client_id'],
    '{redirect_uri}' => $kakaoConfig['redirect_uri'],
    '{client_secret}' => $kakaoConfig['client_secret'],
    '{code}' => $_GET['code']
  );

  $login_token_url = str_replace(array_keys($replace), array_values($replace), $kakaoConfig['login_token_url']);

  $token_data = json_decode(curl_kakao($login_token_url));

  if (empty($token_data)) {
    // throw new Exception("토큰 요청 실패", (__LINE__ * -1));
    echo new Exception("토큰 요청 실패", (__LINE__ * -1));
  }

  if (!empty($token_data->error) || empty($token_data->access_token)) {
    // throw new Exception("토큰 인증 에러", (__LINE__ * -1));
    echo new Exception("토큰 인증 에러", (__LINE__ * -1));
  }

  // 프로필 요청
  $header = array("Authorization: Bearer " . $token_data->access_token);
  $profile_url = $kakaoConfig['profile_url'];
  $profile_data = json_decode(curl_kakao($profile_url, $header));

  if (empty($profile_data) || empty($profile_data->id)) {
    // throw new Exception("프로필 요청 실패", (__LINE__ * -1));
    echo new Exception("프로필 요청 실패", (__LINE__ * -1));
  }

  // 프로필정보 저장 -- DB를 통해 저장하세요

  // 디비에 저장할 이름, 이미지, uniqueId, 이메일 변수
  $profileName = '';
  $profileImage = '';
  $profileUniqueId = '';
  $profileEmail = '';

  /* echo '<pre>';
    print_r($profile_data);
    echo '</pre>'; */

  // 카카오에서 받아온 데이터 중 필요한 데이터만 변수에 저장
  // 프로필 이름, 사진, uniqueId, 이메일
  foreach ($profile_data as $key1 => $value1) {
    if ($key1 == "id") {
      // setcookie('id', time() + 3600 * 24 * 30);
      // 카카오 id를 profileUniqueId 변수에 저장
      $profileUniqueId = $value1;
    } else if ($key1 == 'properties') {
      foreach ($value1 as $key2 => $value2) {
        if ($key2 == 'profile_image') {
          // 카카오 프로필 이미지 값 $profileImage 변수에 저장
          $profileImage = $value2;
        } else if ($key2 == 'nickname') {
          // 카카오 프로필 닉네임을 $profileName 변수에 저장
          $profileName = $value2;
        }
      }
    } else if ($key1 == 'kakao_account') {
      foreach ($value1 as $key3 => $value3) {
        if ($key3 == 'email') {
          $profileEmail = $value3;
        }
      }
    }
  }

  /*  echo $profileName;
   echo "<br>";
   echo $profileImage;
   echo "<br>";
   echo $profileUniqueId;
   echo "<br>";
   echo $profileEmail;
   echo "<br>";
   exit; */

  // 디비 객체 생성
  $db = new Database();

  $kakaoEmailResult = $db->getUsersByKakaoEmail($profileEmail);

  // 세션 사용
  session_start();

  // 기존 회원인지(true) / 비회원인지(false) db 체크
  if ($kakaoEmailResult['type'] == 'kakao') {
    $_SESSION['unique_id'] = $kakaoEmailResult['unique_id'];
    $_SESSION['kakaoEmail'] = "yes";

    // state 초기화
    setcookie('state', '', time() - 300, '/'); // 300 초동안 유효

    header("Location: ../users.php");
  } else {
    $insertResult = $db->insertMember($profileName, $profileImage, $profileUniqueId, $profileEmail);

    if ($insertResult) {
      // 최종 성공 처리
      $res['rst'] = 'success';

      $_SESSION['unique_id'] = $profileUniqueId;
      $_SESSION['kakaoEmail'] = "yes";

      // state 초기화
      setcookie('state', '', time() - 300, '/'); // 300 초동안 유효

      header("Location: ../users.php");
    }
  }


  /*   // 기존 회원일 경우
    if ($is_member === true) {
      $_SESSION['unique_id'] = $emailResult['unique_id'];

      header("Location: ../users.php");
      exit;
    } // 새로 가입일 경우
    else {
      $insertResult = $db->insertMember($profileName, $profileImage, $profileUniqueId, $profileEmail);

      if ($insertResult) {
        // 최종 성공 처리
        $res['rst'] = 'success';

        // echo "<script> alert('테스트 성공') </script>";

        header("Location: ../users.php");

        $_SESSION['unique_id'] = $profileUniqueId;
      }
    } */


} catch (Exception $e) {
  if (!empty($e->getMessage())) {
    echo $res['msg'] = $e->getMessage();
  }
  if (!empty($e->getMessage())) {
    echo $res['code'] = $e->getCode();
  }
}


/* // 성공 처리
if ($res['rst'] == 'success') {
  echo "<script>alert('테스트 성공')</script>";

  header("Location: /login.php");
} // 실패처리
else {

} */
