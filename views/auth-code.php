<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<?php

// 세션 시작
session_start();

// 세션에 authCode가 없는 상태에서 임의로 사용자가 url을 주소 표시줄에 입력해서 들어오면
// 이전 페이지로 보내버리거나 login.php로 보내버림
if (empty($_SESSION['authCode'])) {
  // 변수에 이전 페이지 정보를 저장
  $prevPage = $_SERVER['HTTP_REFERER'] ?? 'login.php';

  header('location:' . $prevPage);
}

?>

<body>
  <div class="wrapper">
    <section class="form signup">

      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; vertical-align: middle">
        <header>인증코드 입력</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <input type="text" name="auth-code" placeholder="이메일에서 받은 인증코드 입력" id="emailAuthCode" required />
          </div>
        </div>

        <div class="field button">
          <input type="submit" value="승인" />
        </div>
      </form>

    </section>
  </div>

  <script src="assets/js/auth-code.js"></script>
</body>
</html>
