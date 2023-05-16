<!-- 헤더 -->
<?php require_once 'header.php'; ?>
<!-- 헤더 -->

<body>

  <div class="wrapper">
    <section class="form signup">

      <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; vertical-align: middle">
        <header>비밀번호 찾기</header> <img src="assets/img/base/talk.png" alt="채팅 아이콘" style="width: 35px; height: 35px; border-radius: 5px;">
      </div>

      <form action="#" autocomplete="off">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field input">
            <label>이름</label>
            <input type="text" name="name" placeholder="홍길동" required />
          </div>
        </div>

        <div class=" field input">
          <label>이메일</label>
          <input type="text" name="email" placeholder="example@naver.com" required />
        </div>

        <div class="field button">
          <input type="submit" value="비밀번호 찾기" />
        </div>
      </form>

    </section>
  </div>

  <script src="assets/js/find-password.js"></script>
</body>
</html>

~
