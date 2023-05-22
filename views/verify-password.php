<?php

require_once '../db/Database.php';

if ($_COOKIE['email']) {

  $database = new Database();
  $resultPassword = $database->getDataByEmail($_COOKIE['email']);
  $resultPassword = $resultPassword['password'];

  if ($resultPassword) {

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

    $descryptedPassword = password_crypt($resultPassword, 'd');
  }

  // 디비 커넥션 끊기
  $database->connectionClose();

} else {
  header("Location: index.php");
}


?>


<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<body>

  <div class="wrapper">
    <section class="form signup">
      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; vertical-align: middle">
        <header>비밀번호 확인</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" autocomplete="off">
        <div class="field input">
          <input type="text" value="<?= $descryptedPassword ?>" />
        </div>

        <div class="field button">
          <input type="submit" value="로그인하기" />
        </div>
      </form>
    </section>


    <script src="assets/js/verify-password.js"></script>
  </div>
</body>
</html>
