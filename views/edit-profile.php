<?php

// 세션 사용
session_start();


// 세션 unique_id 값이 없는데 프로필 정보를 수정하려고 들어오면 login.php로 보내버림
if (!isset($_SESSION['unique_id'])) {
  header("Location: ../login.php");
}

require_once '../db/Database.php';

// 디비 객체 생성
$database = new Database();

// unique_id 값을 기준으로 사용자 데이터 가져오기
$result = $database->getUserDataById($_SESSION['unique_id']);

if ($result) {
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

  $encryptedDbPassword = $result['password'];
  $descryptedPassword = password_crypt($encryptedDbPassword, 'd');

}



// 디비 커넥션 끊기
$database->connectionClose();

?>

<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<body>


  <div class="wrapper">
    <section class="form signup">
      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; vertical-align: middle">
        <header>프로필 수정</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <label>이름</label>
            <input type="text" placeholder="홍길동" id="editProfileName" value="<?= $result['name'] ?>" required />
          </div>
        </div>

        <div class=" field input">
          <label>이메일</label>
          <input type="text" placeholder="example@naver.com" id="editProfileEmail" value="<?= $result['email'] ?>" required />
        </div>
        <div class="field input">
          <label>비밀번호</label>
          <input type="password" id="editProfilePassword" class="password" value="<?= $descryptedPassword ?>" required />
          <i class="fas fa-eye"></i>
        </div>

        <div class="field image">
          <label>사진 변경</label>
          <input type="file" name="image" id="editProfileImage" />
        </div>

        <div class="field button">
          <input type="submit" value="변경 완료" />
        </div>
      </form>
    </section>

    <script src="assets/js/edit-profile.js"></script>
    <script src="assets/js/pass-show-hide.js"></script>
  </div>
</body>
</html>
