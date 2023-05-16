## 채팅 애플리케이션

1. 작성자
<pre>

- 조상호

- hyundai_sangho@naver.com

</pre>

2. 사이트 사용법

<pre>
테스트 할 떄는 서로 다른 브라우저 2개의 브라우저
예를 들어, Edge 브라우저와, Chrome 브라우저를 열고 https://chosangho.site/ 사이트에 들어가서
각자 다른 이름으로 회원 가입을 하면 로그인 처리가 되고 대화 상대방을 선택해서 채팅이 가능함.
</pre>

3. 테스트 영상

- <https://youtu.be/mrMN-qOmRcc>

4. 사이트

- <https://chosangho.site/>

5. 프론트

<pre>HTML
CSS
JS
라이브러리: jquery, font-awesome(아이콘), emojionearea(이모티콘)
</pre>

6. 서버

<pre>PHP
라이브러리: phpdotenv(환경변수), phpmailer(이메일)
</pre>

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
├─ .htaccess                  => /login.php, /chat.php, /users.php, edit-profile.php로 들어오는 url을
│                                /views/login.php, /views/chat.php, /views/users.php, /views/edit-profile.php로 보내버림
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
│  ├─ delete-message.php      =>
│  ├─ edit-profile.php           => 사용자 프로필 수정 화면에서 받아온 POST 데이터 값들을 디비와 연동해 프로필 수정
│  ├─ get-chat.php            => 채팅 데이터를 디비에서 가져오는 php
│  ├─ get-profile.php         =>
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
   ├─ edit-profile.php           => 사용자 프로필 수정 화면
   ├─ header.php              => login.php, index.php, chat.php에 들어가는 헤더 부분 font-awesome 및 style.css
   ├─ login.php               => 로그인 화면
   └─ users.php               => 대화 상대방 검색 및 선택 화면

```

14. API 명세서

| Index | Method | URI                        | Description                               |
| :---: | :----: | :------------------------- | :---------------------------------------- |
|   1   |  GET   | /chat.php?user_id={userId} | userId에 해당하는 상대의 채팅 데이터 조회 |
|   2   |  GET   | /users.php                 | 나를 제외한 전체 대화 상대 조회           |

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

3. <pre>AWS 프리티어 수준의 서버 컴퓨터에서 PHP에 무거운 동작을 수행하게 되면 속도 저하 발생
   가능한 범위에서 브라우저 단에서 자바스크립트로 처리할 수 있는 것들은 처리를 하고
   PHP로 값을 넘겨준 뒤 작업을 하게 되면 사이트 버벅임 예방에 도움이 됨
   예를 들어 사용자가 고용량 이미지 파일을 올리려고 한다면 자바스크립트로 애초에
   이미지 용량 제한을 둬서 막고 저용량 이미지가 업로드된다 하더라도 자바스크립트로
   압축을 해서 용량을 줄인 뒤에 서버에 올라갈 수 있도록 조치
   </pre>

4. <pre>개인 컴퓨터 윈도우에서 돌릴 땐 잘 돌아가더라도 AWS 서버 컴퓨터에서 우분투 리눅스로
   돌릴 때 안 되는 부분이 꽤 많다. 뭔가 돌릴 때 안 되면 권한 문제 root 권한이 아니거나
   해당 폴더나 파일의 권한 문제이다.
   (특히 폴더 같은 경우는 상위 폴더에 권한을 부여하면 자동으로 하위 폴더들도 권한이 생길 것이라
   생각했는데 실제로는 안 돼서 가보면 상위 폴더만 권한이 부여되고 하위 폴더는 따로 권한을 부여해야 하는 경우가
   생기므로 애초에 하위 폴더를 포함해서 권한을 주려면 chmod 명령어에 -R가 같은 옵션을 붙여줘야 한다.)
   
   뭐가 안 돼서 구글링을 해보면 거기에 나온 해답과 나의 실상황이 다른 경우가 많다.
   파일의 위치가 다르거나 해당 파일이 없기도 한다.
   (파일의 위치가 다르다면 find 명령어로 해당 파일을 찾아서 들어가서 해결하면 되지만
   어떤 경우에는 해당 파일이 없기도 하다. 리눅스의 버전이 다르다거나 리눅스의 종류가 다르다거나
   php 버전이 다르다거나 하는 다양한 상황이 생기기 때문에 잘 찾아서 해결해야 한다.
   
   시간 문제는
   php는 php.ini 파일을 찾아서 date.timezone = Asia/Seoul 추가한 뒤에 아파치 서버를 재시작하고
   mysql 같은 경우에도 애초에 지정된 시간이 Asia/Seoul로 시간 설정이 안 되어있기 때문에
   잘 수정을 해야 한다. 개인 컴퓨터에서 xampp에서 돌릴 떄는 전혀 할 필요 없던 설정이었는데
   lampp를 돌리니 mysql 시간 설정이 달라서 하루 가까이 시간 차이가 발생했다.
   우분투 리눅스 기본 시간 설정을 그대로 받아와서 mysql에서 시스템 시간을 사용하는데 그 부분에 문제가
   있어 찾아보니 다른 방법은 다 안 되고 아래 방법으로 해결했다.
   
   1. cd /usr/share/zoneinfo 를 쳐서 바꾸고 싶은 시간대가 있는지 확인합니다.
   
   2. 한국 시간대(Seoul)로 변경해 보겠습니다.
      cp /usr/share/zoneinfo/Asia/Seoul /etc/localtime
   
   3. 콘솔에서 date 명령어를 수행하면 시간대(TimeZone)이 KST로 변경된 것을 확인할 수 있습니다.
   
   4. mysql을 다시 실행 시키기 위해서 sudo su 슈퍼유저로 접속 한 후
      그 다음 콘솔에 service mysql restart 해줘서 mysql을 다시 실행시킵니다.
   )
   </pre>

### 확인 요망

1. 브라우저별 CSS가 제대로 동작하는지 확인

2. 스마트폰, 태블릿 모바일 실제 기기에서 어떻게 CSS가 동작하는지 확인

### 수정 요망

1. 공통 적용되는 css와 각 개별 화면의 css 분리 필요함.

2. 현재 파일명, 변수명, 함수명을 좀 더 가독성 높은 이름으로 변경 필요함.

3. 폴더 구조를 세분화하고 각 기능별로 잘 나눠야 할 필요가 있음.

4. if문 중첩으로 가독성이 떨어지는 부분은 가능한 줄이고 잘 읽히는 코드로 리팩터링 필요함.

### 디비 사용법

1. <pre>xampp - phpmyadmin을 실행</pre>

2. <pre>데이터베이스 chatapp 생성</pre>

3. <pre>phpmyadmin의 가져오기 기능을 이용해서 sql 폴더 내부의 chatapp.sql을 가져오기를 해서 디비 테이블 생성</pre>

4. <pre>db 폴더 내부의 .env.example 파일을 .env로 변경 후에 안에 실제 사용하는 값을 채워넣어 사용</pre>

### 소스 사용법

1. <pre>Github 클론 또는 ZIP 파일 다운로드로 소스 받아오기</pre>

2. <pre>소스는 xampp 기준으로 xampp/htdocs 폴더 내부에 이동시켜 사용</pre>
