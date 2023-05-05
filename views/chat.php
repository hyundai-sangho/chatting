<!-- 헤더 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<?php

// 세션 사용
session_start();

// 세션 unique_id가 없다면 login.php로 이동시킴.
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}

// GET으로 받은 user_id 값이 있다면(즉, 대화 상대자가 있다면)
if (isset($_GET['user_id'])) {
  require_once '../db/Database.php';

  // users.php에서 GET으로 넘어온 user_id 값 $uniqueId 변수에 저장
  $userId = $_GET["user_id"];

  // 디비 객체 생성
  $database = new Database();

  // 디비에서 $userId 토대로 디비에서 데이터 가져오기
  $result = $database->getDataById($userId);


  // 디비 커넥션 끊기
  $database->connectionClose();
}
// GET으로 받은 user_id 값이 없다면 대화 상대가 없다는 의미이므로
// users.php로 이동시켜버림.
else {
  header("location: users.php");
}

?>

<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <a class="back-icon" href="users.php"><i class="fas fa-arrow-left"></i></a>
        <img src="assets/img/<?= $result['img'] ?>" alt="" />
        <div class="details">
          <span>
            <?= $result['name'] ?>
          </span>
          <p>
            <?= $result['status'] ?>
          </p>
        </div>
      </header>

      <div class="chat-box">

      </div>

      <form action="#" class="typing-area" autocomplete="off">

        <input type="hidden" name="outgoing_id" value="<?= $_SESSION['unique_id'] ?>"> <!-- 발신 -->
        <input type="hidden" name="incoming_id" value="<?= $userId ?>"> <!-- 수신 -->
        <input type="text" name="message" class="input-field" placeholder="여기에 메시지를 입력하세요." />
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="assets/js/chat.js"></script>
</body>
</html>
