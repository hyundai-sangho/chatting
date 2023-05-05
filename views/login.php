<!-- 헤더 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

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

  require_once '../db/Database.php';

  $database = new Database();
  // 쿠키로 저장되어 있는 이메일을 토대로 디비에 사용자가 존재하는지 확인
  $result = $database->getDataByEmail($email);

  // 디비 커넥션 끊기
  $database->connectionClose();
}

?>

<body>
  <div class="wrapper">
    <section class="form login">
      <header>로그인</header>
      <form action="#">
        <div class="error-txt"></div>

        <div class="field input">
          <label>이메일</label>
          <input type="text" name="email" placeholder="example@naver.com" value="<?= $result['email'] ?? '' ?>" />
        </div>
        <div class="field input">
          <label>비밀번호</label>
          <input type="password" name="password" id="password" value="<?= $result['password'] ?? '' ?>" />
          <i class="fas fa-eye"></i>
        </div>


        <div>
          <i class="far fa-check-square" id="loginMaintain"></i>
          <span id="loginStatusMaintain">로그인 상태 유지</span>
        </div>

        <div class="field button">
          <input type="submit" value="로그인" />
        </div>
      </form>
      <div class="link">회원가입 안 했다면? <a href="index.php">회원가입</a></div>
    </section>

    <script src="assets/js/pass-show-hide.js"></script>
    <script src="assets/js/login.js"></script>
  </div>
</body>
</html>
