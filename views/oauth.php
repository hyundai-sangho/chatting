<?php

try {

  // ![수정 필요] 카카오 API 환경 설정 파일
  include_once "../config.php";
  include_once "../db/Database.php";

  // 기본 응답 설정
  $res = array('rst' => 'fail', 'code' => (__LINE__ * -1), 'msg' => '');

  $code = $_GET['code'] ?? null;
  $state = $_GET['state'] ?? null;
  $cookieState = $_COOKIE['state'] ?? null;


  // code && state 체크
  if (empty($code) || empty($state) || $state != $cookieState) {
    // throw new Exception("인증 실패", (__LINE__ * -1));
    echo new Exception("인증 실패", (__LINE__ * -1));
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

  // 카카오에서 받아온 데이터 중 필요한 데이터만 변수에 저장
  // 프로필 이름, 사진, uniqueId, 이메일
  foreach ($profile_data as $key1 => $value1) {
    if ($key1 == "id") {
      // 카카오 id를 profileUniqueId 변수에 저장
      $profileUniqueId = intval($value1);
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

  // 디비 객체 생성
  $db = new Database();

  $kakaoEmailResult = $db->getUsersByKakaoEmail($profileEmail);

  // 세션 사용
  session_start();

  if ($profileEmail) {

    // 카카오 소셜 로그인으로 들어온 사용자의 정보가 디비에 이미 존재한다면
    if ($kakaoEmailResult) {
      $_SESSION['unique_id'] = $kakaoEmailResult['unique_id'];
      $_SESSION['kakaoEmail'] = "yes";

      $db->kakaoLoginStatusUpdate($_SESSION['unique_id']);

      // state 초기화
      setcookie('state', '', time() - 300, '/'); // 300초 동안 유효

      header("Location: ../users.php");

      // 카카오 소셜 로그인으로 들어온 사용자 정보가 디비에 없다면 사용자 정보 등록
    } else {
      $insertResult = $db->createKakaoUser($profileName, $profileImage, $profileUniqueId, $profileEmail);

      if ($insertResult) {
        // 최종 성공 처리
        $res['rst'] = 'success';

        $_SESSION['unique_id'] = $profileUniqueId;
        $_SESSION['kakaoEmail'] = "yes";

        // state 초기화
        setcookie('state', '', time() - 300, '/'); // 300초 동안 유효

        header("Location: ../users.php");
      }
    }
  }
} catch (Exception $e) {

  if (!empty($e->getMessage())) {
    echo $res['msg'] = $e->getMessage();
  }

  if (!empty($e->getMessage())) {
    echo $res['code'] = $e->getCode();
  }
}
