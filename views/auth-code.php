<!-- 헤더 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<?php


// 쿠키에 authCode가 없는 상태에서 임의로 사용자가 url을 주소표시줄에 입력해서 들어오면
// login.php로 보내버림
if (empty($_COOKIE['authCode'])) {
  // 변수에 이전 페이지 정보를 저장
  $prevPage = $_SERVER['HTTP_REFERER'] ?? 'login.php';

  header('location:' . $prevPage);
}

?>

<body>
  <img src="assets/img/base/talk.png" alt="채팅 아이콘" id="authChatIcon" class="chatIcon">

  <div class="wrapper">
    <section class="form signup">

      <header>인증코드 입력</header>

      <form action="#" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <input type="text" name="auth-code" placeholder="메일에서 받은 인증코드 입력" required />
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
