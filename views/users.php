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
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<!-- jquery 모달 css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

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


        <a href="#hamburgerModal" rel="modal:open" class="logout">
          <i class="fas fa-bars fa-lg"></i>
        </a>
      </header>

      <div class=" search">
        <span class="text">채팅을 시작할 사용자를 선택</span>
        <input type="text" placeholder="검색할 사람을 입력" />
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">

      </div>
    </section>

    <!-- 햄버거 버튼 모달-->
    <div id="hamburgerModal" class="modal" style="width: 250px; text-align: center;">
      <a href="user/logout.php">
        <button class="hamburgerChildButton" style='background-color: red'>로그아웃</button>
      </a>

      <a href="edit-profile.php">
        <button class="hamburgerChildButton">프로필 수정</button>
      </a>
    </div>

    <!-- jquery.js 추가 :) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

    <!-- jquery modal js 추가 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script src="assets/js/users.js"></script>
  </div>
</body>
</html>
