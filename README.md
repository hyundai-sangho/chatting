## 채팅 애플리케이션

1. 작성자
<pre>

- 조상호

- hyundai_sangho@naver.com

</pre>

2. 사이트 사용법

<pre>
2개의 브라우저를 열어서 예를 들어 Edge 브라우저와, Chrome 브라우저를 열고 https://chosangho.site/
사이트에 들어가서 각자 다른 이름으로 회원 가입을 하면 로그인 처리가 되고 대화 상대방을 선택해서
채팅을 하면 서로 이야기가 가능
</pre>

3. 테스트 영상

- <https://youtu.be/mrMN-qOmRcc>

4. 사이트

- <https://chosangho.site/>

5. 프론트

<pre>HTML, CSS, JS</pre>

6. 서버

<pre>PHP</pre>

7. 디비

<pre>MariaDB</pre>

8. 개발 환경
<pre>

- Windows 10 pro

- VSCode 1.78.0

- Vivaldi Browser 6.0.2979.18

- Chrome Browser 112.0.5615.138

- XAMPP 8.1.2

- MobaXterm v23.0

- WinScp 5.21.7

</pre>

9. 도메인

<pre>GoDaddy (chosangho.site)</pre>

10. 서버 호스팅

<pre>

- aws EC2 (Ubuntu 20.04.6)

- Lampp 7.4.29 설치

- Route 53 - 고대디에서 구매한 도메인 연결

- Certificate Manager https 인증서

- 로드 밸런서 사용(https 적용)

</pre>

11. 데이터베이스

![데이터베이스 테이블](screenshot/databaseTable.png)

12. 실행 장면

![실행 장면](screenshot/play.gif)

13. 프로젝트 구조

```
chatting
├─ .gitignore                 => 깃으로 버전 관리시 필요 없는 데이터는 따로 기록 관리
├─ assets                     => css, js, img 파일들을 관리
├─ .htaccess                  => /login.php, /chat.php, /users.php로 들어오는 url을
│                                /views/login.php, /views/chat.php, /views/users.php로 보내버림
├─ assets                     => css, js, img 파일 묶음 폴더
│  ├─ css
│  │  └─ style.css            => index.php, user.php, login.php, chat.php의 내부 css 파일 하나로 관리(파일 분리 필요)
│  ├─ img                     => 회원가입시에 등록한 프로필 이미지가 이 폴더에 저장됨. 사용자 이미지 로드는 이 폴더를 이용함.
│  └─ js
│     ├─ chat.js              => chat.php의 채팅 화면에서 발생하는 자바스크립트 동작(비동기로 채팅 데이터 받아오기)
│     ├─ login.js             => login.php의 로그인 화면에서 발생하는 자바스크립트 동작(비동기로 로그인 작업 실행)
│     ├─ pass-show-hide.js    => index.php, login.php의 비밀번호 입력 창에서 눈알 아이콘을 눌렀을시 발생하는 동작
│     │                          (비밀번호 속성을 text, password로 토글)
│     ├─ signup.js            => index.php의 회원가입 버튼을 클릭시에 발생하는 동작
│     └─ users.js             => users.php에서 채팅할 상대를 검색하는 동작
├─ composer.json              => 프로젝트에 사용한 vlucas/phpdotenv 라이브러리 정리
├─ composer.lock              => composer install시 생기는 파일로 composer.json에 기록된
│                                라이브러리와 연관된 항목과 버전이 기술됨
├─ db
│  └─ Database.php            => DB 연동 및 데이터 검색, 입력, 회원가입, 로그인, 로그아웃 등의 모든 디비 작업 모음
├─ index.php                  => 회원가입 화면
├─ php
│  ├─ data.php                => users.php 화면에서 대화 상대자 목록을 가져오는 php
│  ├─ get-chat.php            => 채팅 데이터를 디비에서 가져오는 php
│  ├─ insert-chat.php         => 채팅 데이터를 디비에 입력하는 php
│  ├─ login.php               => 로그인을 처리하는 php
│  ├─ loginMaintain.php       => 로그인 상태 유지를 처리하는 php (쿠키에 로그인 이메일 데이터 저장)
│  ├─ logout.php              => 로그아웃 처리를 담당하는 php (쿠키, 세션 제거)
│  ├─ search.php              => users.php 화면에서 검색시 사용자 데이터를 가져오는 php
│  ├─ signup.php              => 회원가입 창에서 입력한 폼 데이터를 디비와 연동해 INSERT 처리
│  └─ users.php               => users.php 화면에서 사용자 전체 데이터를 가져오는 php
├─ README.md                  => 프로젝트에 대한 설명뿐 아니라 사용 방법
├─ screenshot
│  └─ databaseTable.png       => 데이터베이스 테이블 구조 화면
│  └─ play.gif                => 테스트 GIF
├─ sql
│  └─ chatapp.sql             => messages, users 테이블 CREATE 관련 쿼리
└─ views
   ├─ chat.php                => 채팅 화면
   ├─ header.php              => login.php, index.php, chat.php에 들어가는 헤더 부분 font-awesome 및 style.css
   ├─ login.php               => 로그인 화면
   └─ users.php               => 대화 상대방 검색 및 선택 화면

```

### 주의점

1. <pre>
   DB connection을 사용 후 끊어주지 않고 여러 PHP 파일에서
   DB 연결이 쌓이면 데이터를 가져오는 시간이 길어져
   DB 데이터를 받아서 화면에 뿌려주는 데이터들이
   빨리 받아오지 못해 화면에 로딩이 걸려버린다.
   
   DB 사용 후 작업이 끝나면 디비 커넥션을 끊어준다.
   ex) $conn = null;
   </pre>

2. <pre>.htaccess 파일을 사용하기 때문에 nginx 서버 대신에 아파치 사용 요망</pre>

### 디비 사용법

1. <pre>xampp - phpmyadmin을 실행</pre>

2. <pre>데이터베이스 chatapp 생성</pre>

3. <pre>phpmyadmin의 가져오기 기능을 이용해서 sql 폴더 내부의 chatapp.sql을 가져오기를 해서 디비 테이블 생성</pre>

4. <pre>db 폴더 내부의 .env.example 파일을 .env로 변경 후에 안에 실제 사용하는 값을 채워넣어 사용</pre>

### 소스 사용법

1. <pre>Github 클론 또는 ZIP 파일 다운로드로 소스 받아오기</pre>

2. <pre>소스는 xampp 기준으로 xampp/htdocs 폴더 내부에 이동시켜 사용</pre>
