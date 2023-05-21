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

<!-- jquery 모달 css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />


<style>
  /* ( 크롬, 사파리, 오페라, 엣지 ) 동작 */
  .scroll::-webkit-scrollbar {
    display: none;
  }

  .scroll {
    -ms-overflow-style: none;
    /* 인터넷 익스플로러 */
    scrollbar-width: none;
    /* 파이어폭스 */
  }


  /* 스크롤바 생성 */
  .scroll {
    width: 400px;
    height: 400px;
    overflow-y: scroll;
  }
</style>

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
          <input type="password" id="signupPassword" class="password" required />
          <i class="fas fa-eye"></i>
        </div>

        <div style="display: flex; justify-content: space-between;">
          <div class="field image">
            <label>사진 선택</label>
            <input type="file" name="image" id="signupImageFile" accept="image/*" />
          </div>
          <div>
            <img id="signupImage" style="display:none; width: 50px; height: 50px; object-fit: cover; position: relative; top: 5px;">
          </div>
        </div>

        <div style="margin-top: 15px; margin-bottom: 5px;" id="signupPrivacy">
          <input type="checkbox" id="signupPrivacyCheckbox" />

          <a href="#signupPrivacyModal" rel="modal:open">
            <span style="position: relative; top: -0.7px; cursor: pointer;">개인정보 이용약관 동의(필수)</span>
          </a>
        </div>

        <div class="field button">
          <input type="submit" value="회원 가입" />
        </div>
      </form>

      <div class=" link">이미 가입했다면? <a href="login.php">로그인</a>
      </div>
    </section>

    <!-- 햄버거 버튼 모달-->
    <div id="signupPrivacyModal" class="modal">
      <div class="scroll">
        <p style="width: 400px; height: 400px; word-break: keep-all;">

          1. 개인 정보의 수집 항목 및 수집 방법<br><br>
          채팅 사이트에서는 기본적인 회원 서비스 제공을 위한 필수 정보로
          다음의 정보를 수집하고 있습니다. 필수 정보를 입력해주셔야 회원 서비스 이용이 가능합니다.<br><br>

          가. 수집하는 개인 정보의 항목 <br><br>
          * 수집하는 필수 항목<br>
          - 가입 정보 : 성명, 이메일<br><br>

          나. 개인 정보 수집 방법<br><br>
          홈페이지 회원 가입을 통한 수집 <br><br>

          2. 개인 정보의 수집/이용 목적 및 보유/이용 기간<br><br>
          채팅 사이트에서는 정보 주체의 회원 가입일로부터 서비스를 제공하는 기간 동안에 한하여
          채팅 서비스를 이용하기 위한 최소한의 개인정보를 보유 및 이용 하게 됩니다.<br><br>

          회원가입 등을 통해 개인정보의 수집·이용, 제공 등에 대해 동의하신 내용은 언제든지 철회하실 수 있습니다.

        </p>
      </div>
    </div>

    <!-- jquery.js 추가 :) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

    <!-- jquery modal js 추가 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>

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
