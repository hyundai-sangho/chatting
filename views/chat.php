<?php

// 세션 사용
session_start();

// 세션 unique_id가 없다면 login.php로 이동시킴.
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}

// GET으로 받은 user_id 값이 있다면(즉, 대화 상대자가 있다면)
if (isset($_GET['user_id'])) {

  // // users.php에서 GET으로 넘어온 user_id 값 $uniqueId 변수에 저장
  // $userId = $_GET["user_id"];

  setcookie("talkFriend", $_GET['user_id'], time() + (86400), "/"); // 쿠키 생존 기간 1일
}
// GET으로 받은 user_id 값이 없다면 대화 상대가 없다는 의미이므로
// users.php로 이동시켜버림.
else {
  header("location: users.php");
}

?>

<!-- 헤더 -->
<!-- 기본 css, font-awesome, pwa manifest.json 포함 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<link rel="stylesheet" href="//unpkg.com/emojionearea/dist/emojionearea.min.css">


<body>

  <div class="wrapper">
    <section class="chat-area">
      <header id="header">

      </header>

      <div class="chat-box">

      </div>

      <form action="#" class="typing-area" autocomplete="off">
        <input type="hidden" value="<?= $_SESSION['unique_id'] ?>" id="outgoingId"> <!-- 발신 -->
        <input type="hidden" value="<?= $_GET['user_id'] ?>" id="incomingId"> <!-- 수신 -->
        <textarea type="text" class="chattingMessage" style="position: absolute; max-height: 600px;" placeholder="여기에 메시지를 입력하세요." id="chatMessage"></textarea>
        <i class="fa-solid fa-image" style="position: relative; top: 8.2px; left: -15%; font-size: 15px; color: grey;" id="chatUploadImage"></i>
        <input type="file" style="display: none;" onchange="insertChat()" id="chatImage" />
        <button style="position: relative; left: 10px; margin-left: -25px; font-size: 25px;" id="messageSendButton"><i class="far fa-paper-plane fa-spin"></i></button>
      </form>
    </section>
  </div>

  <script src="//unpkg.com/jquery/dist/jquery.min.js"></script>
  <script src="//unpkg.com/emojionearea/dist/emojionearea.min.js"></script>
  <script src="assets/js/chat.js"></script>
</body>
</html>
