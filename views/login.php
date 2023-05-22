<?php

// 세션 사용
session_start();

// 세션 unique_id 값이 존재하는 상태에서 로그인 페이지로 들어오면 users.php로 보내버림
// 회원가입이나 로그인을 다시 하려면 무조건 로그아웃을 하도록 설정
if (isset($_SESSION['unique_id'])) {
  header("Location: users.php");
}

// 로그인 창에서 로그인 상태 유지 버튼을 클릭하면 생기는 쿠키 email이 존재한다면
// 자동으로 이메일 입력창과 비밀번호창에 값이 채워짐
// 바로 로그인 가능
if (isset($_COOKIE['email'])) {
  $email = $_COOKIE['email'];

  include_once '../db/Database.php';

  $database = new Database();
  // 쿠키로 저장되어 있는 이메일을 토대로 디비에 사용자가 존재하는지 확인
  $result = $database->getDataByEmail($email);





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

  $descryptedPassword = password_crypt($result['password'], 'd');





  // 디비 커넥션 끊기
  $database->connectionClose();
}


?>

<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<body>

  <div class="wrapper">
    <section class="form login">
      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc;">
        <header>로그인</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" autocomplete="off">
        <div class="error-txt"></div>

        <div class="field input">
          <label>이메일</label>
          <input type="text" placeholder="example@naver.com" value="<?= $result['email'] ?? '' ?>" id="loginEmail" required />
        </div>
        <div class="field input">
          <label>비밀번호</label>
          <input type="password" class="password" value="<?= $descryptedPassword ?? '' ?>" id="loginPassword" required />
          <i class="fas fa-eye"></i>
        </div>


        <div style="display: flex; justify-content: space-between;">
          <div>
            <i class="far fa-check-square" id="loginMaintain"></i>
            <span id="loginStatusMaintain">로그인 상태 유지</span>
          </div>

          <div>
            <a href="find-password.php" style="color: black;">
              <i class="fa-solid fa-key" id="passwordFind"></i>
              <span id="passwordFindText">비밀번호 찾기</span>
            </a>
          </div>
        </div>

        <div class="field button">
          <input type="submit" value="이메일 로그인" />
        </div>
      </form>

      <div class="link">회원가입 안 했다면? <a href="index.php">회원가입</a></div>
    </section>

    <script src="assets/js/pass-show-hide.js"></script>
    <script src="assets/js/login.js"></script>
  </div>
</body>
</html>
