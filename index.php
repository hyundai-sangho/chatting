<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php include_once 'views/header.php'; ?>
<!-- 헤더 -->

<?php

// 세션 사용
session_start();

// 세션 unique_id 값이 존재하는데 회원가입 페이지로 들어오면 users.php로 보내버림
// 회원가입이나 로그인을 다시 하려면 무조건 로그아웃을 하도록 설정
if (isset($_SESSION['unique_id'])) {
  header("Location: users.php");
}

?>

<body>

  <div class="wrapper">
    <section class="form signup">


      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; vertical-align: middle">
        <header>회원가입</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" enctype="multipart/form-data" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <label>이름</label>
            <input type="text" name="name" placeholder="홍길동" id="signupName" required />
          </div>
        </div>

        <div class="field input">
          <label>이메일</label>
          <input type="text" name="email" placeholder="실제 사용하는 이메일 입력" id="signupEmail" id="signupEmail" required />
          <i id="emailAuthentication">인증</i>
        </div>

        <div class="field input">
          <label>이메일 인증코드</label>
          <input type="text" name="authCode" placeholder="이메일에서 받은 인증코드를 입력" id="signupAuthCode" required />
          <i id="emailAuthenticationTimer"></i>
        </div>

        <div class="field input">
          <label>비밀번호</label>
          <input type="password" name="password" id="password" required />
          <i class="fas fa-eye" class="eyePassword"></i>
        </div>

        <div class="field image">
          <label>사진 선택</label>
          <input type="file" name="image" id="signupImageFile" accept="image/*" />
        </div>

        <div class="field button">
          <input type="submit" value="회원 가입" />
        </div>
      </form>

      <div class=" link">이미 가입했다면? <a href="login.php">로그인</a>
      </div>
    </section>

    <!-- Sweet Alert 팝업 -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- 비밀번호 부분의 눈알 클릭시 동작 -->
    <script src="assets/js/pass-show-hide.js"></script>
    <!-- 회원 가입 버튼 클릭시 일어나는 동작 -->
    <script src="assets/js/signup.js"></script>
    </script>
  </div>
</body>
</html>
