<?php

// 세션 사용
session_start();

// 세션 unique_id 값이 없다면(즉, 현재 로그인 또는 회원가입을 해서 세션이 생성된 상태가 아니라면)
// login.php로 이동시켜 버림
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}

?>

<!-- 헤더 -->
<?php require_once __DIR__ . '/header.php'; ?>
<!-- 헤더 -->

<body>
  <div class="wrapper">
    <section class="users">
      <header>

        <?php

        require_once '../db/Database.php';

        // 로그인 혹은 회원가입 시에 만들어진 세션 id 값을 $sessionId 변수에 저장
        $sessionId = $_SESSION['unique_id'];

        // 디비 객체 생성
        $database = new Database();

        // $session를 토대로 디비에서 데이터 가져오기
        $database->getDataBySessionId($sessionId);

        // 디비 커넥션 끊기
        $database->connectionClose();

        ?>


        <a href="php/logout.php" class="logout">로그아웃</a>
      </header>
      <div class="search">
        <span class="text">채팅을 시작할 사용자를 선택</span>
        <input type="text" placeholder="검색할 이름을 입력" />
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">

      </div>
    </section>

    <script src="assets/js/users.js"></script>
  </div>
</body>
</html>
