<!-- 헤더 -->
<?php require_once 'views/header.php'; ?>
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
      <header>회원가입</header>
      <form action="#" enctype="multipart/form-data" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <label>이름</label>
            <input type="text" name="name" placeholder="홍길동" required />
          </div>
        </div>

        <div class="field input">
          <label>이메일</label>
          <input type="text" name="email" placeholder="example@naver.com" required />
        </div>
        <div class="field input">
          <label>비밀번호</label>
          <input type="password" name="password" id="password" required />
          <i class="fas fa-eye"></i>
        </div>
        <div class="field image">
          <label>사진 선택</label>
          <input type="file" name="image" required />
        </div>
        <div class="field button">
          <input type="submit" value="회원 가입" />
        </div>
      </form>
      <div class="link">이미 가입했다면? <a href="login.php">로그인</a></div>
    </section>

    <script src="assets/js/pass-show-hide.js"></script>
    <script src="assets/js/signup.js"></script>
  </div>
</body>
</html>
