<?php


/* php dotenv 사용을 위해 vendor 폴더 내부의 autoload.php require 함. */
require_once "../vendor/autoload.php";

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/* monolog 추가 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 로거 채널 생성
$log = new Logger('chatSite');

// log/info.log 파일에 로그 생성. 로그 레벨은 INFO
$log->pushHandler(new StreamHandler('../log/info.log', Logger::INFO));

/**
 * Database 클래스
 *
 * __construct() 생성자: 디비 연결 HOST, USER, PW, DBNAME 설정
 *
 * 1. connection() 메소드
 * 디비 연결 커넥션
 * return $conn
 *
 * 2. getDataById($id) 메소드
 * users.php에서 user_id를 GET으로 보낸 값을 토대로
 * 데이터를 가져와 프로필 사진, 이름, 접속 상태를 표시
 * return $resultData
 *
 * 3. getDataBySessionId($sessionId) 메소드
 * 회원가입 혹은 로그인 시에 생성되는 세션 아이디를 토대로
 * 데이터를 가져와 프로필 사진, 이름, 접속 상태를 표시
 *
 * 4. getAllData() 메소드
 * users 테이블의 전체 사용자 데이터를 가져와 화면에 보여줌.
 *
 * 5. login($email, $password) 메소드
 * $email, $password 값에 해당하는 사용자가 있는지 체크해서
 * 로그인이 가능하다면 로그인을 시켜버림
 *
 * 6. getDataBySearchTerm($searchTerm) 메소드
 * users.php에서 POST로 받아온 $searchTerm 변수로 users 테이블의 사용자 데이터 가져오기
 *
 * 7.signUp($name, $email, $password)
 * 회원가입 창에서 넘어온 이름, 이메일, 비밀번호를 users 디비 테이블에 저장
 *
 * 8. getDataByEmail($email)
 * 로그인 창에서 로그인 상태 유지를 위해 이메일 데이터를 바탕으로
 * users 디비 테이블에서 데이터 존재 유무를 확인
 *
 * 9.  insertChatMessage($message, $outgoingId, $incomingId, $imageFileName)
 * 채팅 데이터 입력
 *
 * 10. getMessagesDataById($outgoingId, $incomingId)
 * 채팅 메시지 가져오기
 *
 * 11. logout($sessionId)
 * 로그 아웃
 *
 * 12. getUserDataById($unique_id)
 * unique_id로 사용자 정보 가져오기
 *
 * 13. deleteMessage($deleteMessageId)
 * 메시지 삭제
 *
 * 14. getPasswordRandomAuthCode()
 * 이메일 인증 코드 가져오기
 *
 * 15. getUsersDataByEmailAndPassword($email, $password)
 * 이메일과 비밀번호로 사용자 정보 가져오기
 *
 * 16. createKakaoUser($profileName, $profileImage, $profileUniqueId, $profileEmail)
 * 카카오 소셜 로그인을 통해 들어온 사용자 처음 로그인시 디비에 사용자 정보 등록
 *
 * 17. kakaoLoginStatusUpdate($unique_id)
 * 카카오 접속 상태 변경(비접속에서 접속으로 변경)
 *
 * 18. connectionClose() 메소드
 * 커넥션 끊기
 */
class Database
{
  private $host;
  private $user;
  private $password;
  private $dbname;
  private $conn;

  /**
   * __construct() 생성자: 디비 연결 HOST, USER, PW, DBNAME 설정
   */
  public function __construct()
  {
    // db/.env 파일 내부의 값들을 하나 하나 불러와
    // host, user, password, dbname에 집어넣어버림.
    $this->host = $_ENV['DB_HOST'];
    $this->user = $_ENV['DB_USER'];
    $this->password = $_ENV['DB_PASSWORD'];
    $this->dbname = $_ENV['DB_NAME'];
  }

  /**
   * connection() 메소드: return $conn
   */
  public function connection()
  {
    try {
      $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo '연결 에러: ' . $e->getMessage();
    }

    return $this->conn;
  }


  /**
   * users.php에서 user_id를 GET으로 보낸 값을 토대로
   * 데이터를 가져와 프로필 사진, 이름, 접속 or 비접속 상태를 표시
   */
  public function getDataById($id)
  {
    $sql = "SELECT * FROM users WHERE unique_id = :uniqueId";
    $stmt = $this->connection()->prepare($sql);
    $stmt->bindParam(':uniqueId', $id);
    $stmt->execute();

    $row_count = $stmt->rowCount();

    if ($row_count > 0) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return $result;
  }

  /**
   * 회원가입 혹은 로그인 시에 생성되는 세션 아이디를 토대로
   * 데이터를 가져와 프로필 사진, 이름, 접속 or 비접속 상태를 표시
   */
  public function getDataBySessionId($sessionId)
  {
    $sql = "SELECT * FROM users WHERE unique_id = :sessionId";
    $stmt = $this->connection()->prepare($sql);
    $stmt->bindParam(':sessionId', $sessionId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $row_count = $stmt->rowCount();

    if ($row_count > 0 && $result['type'] == 'email') {
      // users.php 에서 로그인한 사용자의 이름, 사진, 접속 or 비접속 상태를 표시
      // 프로필 사진 클릭시 js/users.js 내부의 submitForm() 함수가 실행되면서 form submit 실행
      // edit-profiles.php 로 POST로 값이 넘어가면서 사용자 정보 수정 화면으로 이동
      echo "<form action='edit-profile.php' method='POST' id='profileForms'>
              <div class='content' onclick='submitForm()' style='cursor: pointer'>
                <input type='hidden' name='unique_id' value='$result[unique_id]'>
                <img src='$result[img]' alt='프로필 사진' />

                <div class='details' style='margin-top: 12px;'>
                  <span>
                    $result[name]
                  </span>
                </div>
              </div>
            </form>";
    }
    // 카카오 소셜 로그인을 한 사용자는 프로필 수정이 불가능하도록 설정
    elseif ($row_count > 0 && $result['type'] == 'kakao') {
      // users.php 에서 로그인한 사용자의 이름, 사진, 접속 or 비접속 상태를 표시
      // 프로필 사진 클릭시 js/users.js 내부의 submitForm() 함수가 실행되면서 form submit 실행
      // edit-profiles.php 로 POST로 값이 넘어가면서 사용자 정보 수정 화면으로 이동
      echo "<form action='edit-profile.php' method='POST' id='profileForms'>
              <div class='content' style='cursor: pointer'>
                <input type='hidden' name='unique_id' value='$result[unique_id]'>
                <img src='$result[img]' alt='프로필 사진' />

                <div class='details' style='margin-top: 12px;'>
                  <span>
                    $result[name]
                  </span>
                </div>
              </div>
            </form>";
    }
  }

  /**
   * users 테이블의 전체 사용자 데이터를 가져와 화면에 보여줌.
   */
  public function getAllData()
  {
    session_start();

    // users 테이블의 전체 데이터 가져오기 SQL
    $sql = "SELECT * FROM users WHERE NOT unique_id = :uniqueId";

    // 실행할 명령문 준비
    $stmt = $this->connection()->prepare($sql);

    // :uniqueId를 $_SESSION['unique_id'] 값으로 바인딩
    $stmt->bindParam(':uniqueId', $_SESSION['unique_id']);

    // 실행
    $stmt->execute();

    // 명령문에 해당하는 사용자가 몇 명인지 확인
    $row_count = $stmt->rowCount();

    $output = '';

    // $row_count가 0이라면 현재 나 자신 밖에 없다는 의미로 대화 가능한 상태가 없는 상태
    // $row_count가 1 이상이라면 사용자가 나 이외에도 있다는 뜻이므로 data.php를 require해서
    // 다른 사용자도 화면에 나타나게 함.
    if ($row_count == 0) {
      $output .= '대화 가능한 상대가 없네요.';


    } elseif ($row_count >= 1) {
      include_once '../user/data.php';
    }

    // $output 등록된 데이터 화면에 출력
    echo $output;
  }

  /**
   * $email, $password 값에 해당하는 사용자가 있는지 체크해서
   * 로그인이 가능하다면 로그인을 시켜버림
   */
  public function login($email, $password)
  {
    // 세션 사용
    session_start();

    function password_crypt($string, $action = 'e') // $action 값은 기본값을 e(ncryted)로 한다.
    {
      $secret_key = 'chosangho_secret_key';
      $secret_iv = 'chosangho_secret_iv';

      $output = false;
      $encrypt_method = "AES-256-CBC";
      $key = hash('sha256', $secret_key);
      $iv = substr(hash('sha256', $secret_iv), 0, 16);

      if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
      } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
      }

      return $output;
    }

    // $email, $password 변수에 값이 없다면 "모든 입력 필드가 필요합니다." 출력
    if (!empty($email) && !empty($password)) {
      $selectUsersTableByEmailQuery = "SELECT * FROM users WHERE email = :email AND type = 'email' ";
      $stmt = $this->connection()->prepare($selectUsersTableByEmailQuery);
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      $row_count = $stmt->rowCount();

      // $email에 해당하는 데이터가 users 디비 테이블에 있다면
      if ($row_count > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // 디비에서 가져온 비밀번호를 복호화한 뒤의 값을 $descryptedPassword 변수에 저장
        $decryptedPassword = password_crypt($row['password'], 'd');

        // 세션 unique_id 값에 디비에서 받아온 unique_id 값 삽입
        $_SESSION['unique_id'] = $row['unique_id'];

        // 로그인에서 입력한 비밀번호와 디비에서 가져온 비밀번호를 복호화 한 후의 값이 같다면
        if ($password == $decryptedPassword) {
          $selectSql = "SELECT * FROM users WHERE unique_id = :sessionId AND type = 'email' ";
          $selectResult = $this->connection()->prepare($selectSql);
          $selectResult->bindParam(':sessionId', $_SESSION['unique_id']);
          $selectResult->execute();

          $selectResultData = $selectResult->fetch(PDO::FETCH_ASSOC);

          $sessionUniqueIdArray = unserialize(base64_decode($selectResultData['session_unique_id']));

          if (count($sessionUniqueIdArray) == 3) {
            echo "현재 로그인 상태인 기기가 3개라 더 이상은 로그인이 불가능합니다. 기존 로그인한 기기를 로그아웃 해주세요.";
            exit;
          }

          // 랜덤 숫자 구하기
          $randomId = rand(time(), 10000000);

          array_push($sessionUniqueIdArray, $randomId);
          $sessionUniqueIdEncode = base64_encode(serialize($sessionUniqueIdArray));

          $sql = "UPDATE users SET status = '접속', session_unique_id = :sessionUniqueId WHERE unique_id = :sessionId";
          $result = $this->connection()->prepare($sql);
          $result->bindParam(':sessionId', $_SESSION['unique_id']);
          $result->bindParam(':sessionUniqueId', $sessionUniqueIdEncode);
          $result->execute();

          echo "성공";

        } else {
          echo nl2br("로그인시 입력한 비밀번호가\n디비의 비밀번호와 다릅니다.");
        }

      } else {
        echo nl2br("로그인시 입력한 이메일의\n데이터가 디비에 없습니다.");
      }

    } else {
      echo "모든 입력 필드가 필요합니다.";
    }
  }

  /**
   * users.php에서 POST로 받아온 $searchTerm 변수로 users 테이블의 사용자 데이터 가져오기
   */
  public function getDataBySearchTerm($searchTerm)
  {
    // $searchTerm 변수의 공백 제거
    $searchTerm = preg_replace("/\s+/", "", $searchTerm);

    $selectUsersBySearchTermQuery = "SELECT * FROM users WHERE NOT unique_id = :uniqueId AND name LIKE :searchTerm";
    $stmt = $this->connection()->prepare($selectUsersBySearchTermQuery);

    $search_term = "%{$searchTerm}%";
    $stmt->bindParam(':uniqueId', $_SESSION['unique_id']);
    $stmt->bindParam(':searchTerm', $search_term);

    $stmt->execute();

    $row_count = $stmt->rowCount();

    $output = '';

    if ($row_count >= 1) {
      include_once '../user/data.php';
    } elseif ($row_count == 0) {
      $output .= "검색어와 관련된 사용자가 없습니다.";
    }

    echo $output;
  }

  /**
   * 회원가입 창에서 넘어온 이름, 이메일, 비밀번호를 users 디비 테이블에 저장
   */
  public function signUp($name, $email, $password, $imageFile)
  {
    global $log;

    $sql = "SELECT * FROM users WHERE email = :email AND `type` ='email' ";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':email', $email);
    $result->execute();

    $row_count = $result->rowCount();

    // 회원가입 창에서 입력한 이메일과 같은 이메일이 디비에 존재한다면
    // "이미 존재하는 이메일입니다." 화면에 출력
    if ($row_count > 0) {
      $log->info("{ message: '이미 존재하는 이메일입니다.', location: 'db/Database.php, signUp() 함수' }");
      echo "{ message: 이미 존재하는 이메일입니다. }";
    }

    // 같은 이메일이 없다면 디비에 저장
    else {
      // 회원가입 화면에서 프로필 이미지 선택을 따로 안 했다면 프로필 이미지는 "default.png"로 들어감.
      if ($imageFile == null) {
        $status = "접속";
        $type = "email";

        // 랜덤 숫자 구하기
        $randomId = rand(time(), 10000000);
        $randomIdEncode = base64_encode(serialize(array($randomId)));

        // 기본 이미지(회원가입 화면에서 프로필 이미지를 선택하지 않았다면 기본 이미지로 업로드)
        $default_image = "https://static.vecteezy.com/system/resources/thumbnails/009/734/564/small/default-avatar-profile-icon-of-social-media-user-vector.jpg";

        $sql2 = "INSERT INTO users (unique_id, name, email, password, img, status, session_unique_id, type ) VALUES (:unique_id, :name, :email, :password, :img, :status, :session_unique_id, :type)";

        $result2 = $this->connection()->prepare($sql2);

        $result2->bindParam(':unique_id', $randomId);
        $result2->bindParam(':name', $name);
        $result2->bindParam(':email', $email);
        $result2->bindParam(':password', $password);
        $result2->bindParam(':img', $default_image);
        $result2->bindParam(':status', $status);
        $result2->bindParam(':session_unique_id', $randomIdEncode);
        $result2->bindParam(':type', $type);

        $result2->execute();

        $insertRowCount = $result2->rowCount();

        // 회원정보 데이터를 입력하고 가입한 이메일로 다시 쿼리로 돌려보면서 제대로 디비에 들어갔는지 확인
        if ($insertRowCount > 0) {
          $sql3 = "SELECT * FROM users WHERE email = :email AND `type` = 'email' ";
          $result3 = $this->connection()->prepare($sql3);
          $result3->bindParam(':email', $email);
          $result3->execute();

          $row_count2 = $result3->rowCount();

          // 회원정보가 제대로 입력이 됐으니 디비에서 받아온 unique_id 값을 세션 unique_id 값에 삽입
          if ($row_count2 > 0) {
            $row = $result3->fetch(PDO::FETCH_ASSOC);

            // 여기까지 왔다면 회원 데이터가 제대로 디비에 저장이 되고
            // 세션 unique_id에 디비에서 받아온 unique_id 값 저장
            $_SESSION['unique_id'] = $row['unique_id'];

            // 회원가입 완료 후 세션 authCode 제거(이메일 인증코드)
            unset($_SESSION['authCode']);

            http_response_code(200);
            echo "{ code : '200', message : '회원 가입 성공', location: 'db/Database.php, signUp() 함수' }";
            exit;
          }
        }
      }

      // 회원가입 화면에서 프로필 사진을 넣었다면
      else {

        $status = "접속";
        $type = "email";

        // 랜덤 숫자 구하기
        $randomId = rand(time(), 10000000);
        $randomIdEncode = base64_encode(serialize(array($randomId)));

        $sql2 = "INSERT INTO users (unique_id, name, email, password, img, status, session_unique_id, type ) VALUES (:unique_id, :name, :email, :password, :img, :status, :session_unique_id, :type)";

        $result2 = $this->connection()->prepare($sql2);

        $result2->bindParam(':unique_id', $randomId);
        $result2->bindParam(':name', $name);
        $result2->bindParam(':email', $email);
        $result2->bindParam(':password', $password);
        $result2->bindParam(':img', $imageFile);
        $result2->bindParam(':status', $status);
        $result2->bindParam(':session_unique_id', $randomIdEncode);
        $result2->bindParam(':type', $type);

        $result2->execute();

        $insertRowCount = $result2->rowCount();

        if ($insertRowCount > 0) {
          $sql3 = "SELECT * FROM users WHERE email = :email";
          $result3 = $this->connection()->prepare($sql3);
          $result3->bindParam(':email', $email);
          $result3->execute();

          $row_count2 = $result3->rowCount();

          if ($row_count2 > 0) {
            $row = $result3->fetch(PDO::FETCH_ASSOC);

            // 여기까지 왔다면 회원 데이터가 제대로 디비에 저장이 되고
            // 세션 unique_id에 디비에서 받아온 unique_id 값 저장
            $_SESSION['unique_id'] = $row['unique_id'];

            // 회원가입 완료 후 세션 authCode 제거(이메일 인증코드)
            unset($_SESSION['authCode']);

            http_response_code(200);
            echo "{ code : '200', message : '회원 가입 성공', location: 'db/Database.php, signUp() 함수'  }";
          }
        }

      }
    }
  }

  /**
   * 로그인 창에서 로그인 상태 유지를 위해 이메일 데이터를 바탕으로
   * users 디비 테이블에서 데이터 존재 유무를 확인
   */
  public function getDataByEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':email', $email);
    $result->execute();

    return $result->fetch(PDO::FETCH_ASSOC);
  }

  public function getUsersByKakaoEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = :email AND `type` = 'kakao' LIMIT 1";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':email', $email);
    $result->execute();

    return $result->fetch(PDO::FETCH_ASSOC);
  }

  public function insertChatMessage($message, $outgoingId, $incomingId, $imageFileName)
  {

    // 메시지가 비어있지 않다면 => 메시지가 있다면
    if (!empty($message)) {

      try {
        $sql = "INSERT INTO messages (msg, outgoing_msg_id, incoming_msg_id) VALUES (:message, :outgoing_id, :incoming_id)";
        $result = $this->connection()->prepare($sql);
        $result->bindParam(':message', $message);
        $result->bindParam(':outgoing_id', $outgoingId);
        $result->bindParam(':incoming_id', $incomingId);
        $result->execute();

        $row = $result->rowCount();

        if ($row > 0) {
          echo "성공";
        }
      } catch (PDOException $e) {
        echo "메시지 업로드 중 insertChatMessage() 함수 INSERT 쿼리 실패" . $e->getMessage();
      }

      // 메시지가 비었다면 => 즉, 사진 파일을 올렸다면(사진 파일을 올릴 시에는 텍스트는 삽입 안 함.)
    } else {

      try {
        $sql = "INSERT INTO messages (outgoing_msg_id, incoming_msg_id, image) VALUES (:outgoing_id, :incoming_id, :image)";
        $result = $this->connection()->prepare($sql);
        $result->bindParam(':outgoing_id', $outgoingId);
        $result->bindParam(':incoming_id', $incomingId);
        $result->bindParam(':image', $imageFileName);
        $result->execute();

        echo "성공";
      } catch (PDOException $e) {
        echo "사진 업로드 중 insertChatMessage() 함수 INSERT 쿼리 실패" . $e->getMessage();
      }
    }
  }

  public function getMessagesDataById($outgoingId, $incomingId)
  {
    $sql = "SELECT * FROM messages m
            LEFT JOIN users u ON u.unique_id = m.outgoing_msg_id
            WHERE (outgoing_msg_id = :outgoingId AND incoming_msg_id = :incomingId) OR (outgoing_msg_id = :incomingId AND incoming_msg_id = :outgoingId) ORDER BY m.id";

    $result = $this->connection()->prepare($sql);
    $result->bindParam(':outgoingId', $outgoingId);
    $result->bindParam(':incomingId', $incomingId);
    $result->execute();

    $results = $result->fetchAll(PDO::FETCH_ASSOC);
    $row_count = $result->rowCount();


    $output = '';

    if ($row_count > 0) {

      foreach ($results as $row) {

        $currentDate = new DateTime('now');
        $currentDate = $currentDate->format('Y-m-d');
        $substringCurrentDate = substr($currentDate, 0, 10);
        $currentDate = new DateTime($substringCurrentDate);

        $substringWriteDate = substr($row['created_at'], 0, 10);
        $writeDate = new DateTime($substringWriteDate);
        $diff = $currentDate->diff($writeDate);

        // 글 작성일과 오늘 날짜가 하루 이상 차이가 난다면 $diffDay 변수에 날짜 저장
        if ($diff->days >= 1) {

          $selectMsgIdQuery = "SELECT id FROM `messages` WHERE ((outgoing_msg_id = $outgoingId AND incoming_msg_id = $incomingId) OR (outgoing_msg_id = $incomingId AND incoming_msg_id = $outgoingId)) AND DATE(created_at) = '$substringWriteDate' ORDER BY id DESC LIMIT 1";

          $selectMsgIdQueryResult = $this->connection()->prepare($selectMsgIdQuery);
          $selectMsgIdQueryResult->execute();
          $selectMsgIdQueryResultRow = $selectMsgIdQueryResult->fetch(PDO::FETCH_ASSOC);

          if ($selectMsgIdQueryResultRow['id'] == $row['id']) {

            // 날짜 기준으로 요일 계산하기
            $yoil = array("일", "월", "화", "수", "목", "금", "토");
            $yoil = $yoil[date('w', strtotime($substringWriteDate))];


            // 날짜에 년, 월, 일, 요일 넣기
            $substringWriteDate = substr($substringWriteDate, 0, 4) . "년 " . substr($substringWriteDate, 5, 2) . "월 " . substr($substringWriteDate, 8, 2) . "일 " . "($yoil)";
            $diffDay = $substringWriteDate;

            $diff = "$diffDay";
          } else {
            $diff = null;
          }

        } else {
          $diff = null;
        }

        // 디비에서 msg_time 가져온 뒤에 시간과 분만 따로 추출해서 $msgTime 변수에 저장
        $msgTime = substr($row['created_at'], 10, 6) . '분';


        $rowMsg = nl2br($row['msg']);

        // 메시지 보낸 사람 = 나
        if ($row['outgoing_msg_id'] == $outgoingId) {
          if ($row['image']) {

            $output .= "<div class='chat outgoing'>
                                  <div class='details'>
                                    <span style='position: relative; left: -70px; top: 30px;'>$msgTime</span>
                                    <i id='myImageCloseIcon' class='fas fa-times myImageCloseIcon' onclick='deleteMessage($row[id])'></i>
                                    <img src='$row[image]' style='width: 100%; object-fit: cover; border-radius: 5px;'>

                                    <a href='$row[image]' download style='position: relative; top: -30px; left: -70px;'>
                                      <button style='cursor: pointer;'>
                                        다운로드
                                      </button>
                                    </a>

                                    <span id='myImgDiffDay'>$diff</span>

                                  </div>
                                </div>";
          } else {
            $output .= "<div class='chat outgoing'>
                                  <div class='details'>
                                    <span style='position: relative; left: -70px; top: 30px;'>$msgTime</span>
                                    <p> $rowMsg <i id='myMessageCloseIcon' class='fas fa-times myImageCloseIcon' onclick='deleteMessage($row[id])'></i> </p>

                                    <span id='myImgDiffDay'>$diff</span>

                                  </div>
                                </div>";
          }

        } else { // 메시지 받는 사람 = 다른 사람
          if ($row['image']) {
            if (isset($diff)) {
              $output .= "<div class='chat incoming'>
                                    <img src='$row[img]' alt='프로필 사진' />

                                    <div class='details' style='position: relative;'>
                                      <img src='$row[image]' style='margin-left: 10px; width: 200px; height: 200px; object-fit: cover; border-radius: 5px;'>
                                      <span style='position: absolute; left: 220px; top: 10px; width: 50%;'>$msgTime</span>

                                      <a href='$row[image]' download>
                                        <button style='position:relative; left: 220px; top: -25px; cursor: pointer;'>
                                          다운로드
                                        </button>
                                      </a>
                                      <span style='position: relative; top: 1px; left: -50px; color: red; font-weight: bold'>$diff</span>
                                    </div>
                                  </div>";
            } else {
              $output .= "<div class='chat incoming'>
                                  <img src='$row[img]' alt='프로필 사진' />

                                    <div class='details' style='position: relative;'>
                                      <img src='$row[image]' style='margin-left: 10px; width: 200px; height: 200px; object-fit: cover; border-radius: 5px;'>
                                      <span style='position: absolute; left: 220px; top: 10px; width: 50%;'>$msgTime</span>

                                      <a href='$row[image]' download>
                                        <button style='position:relative; left: 220px; top: -25px; cursor: pointer;'>
                                          다운로드
                                        </button>
                                      </a>
                                    </div>
                                  </div>";
            }


          } else {
            $output .= "<div class='chat incoming'>
                                  <img src='$row[img]' alt='프로필 사진' />
                                  <div class='details'>
                                    <span style='position: relative; left: 210px; top: 30px;'>$msgTime</span>
                                    <p> $rowMsg </p>
                                    <span style='position: relative; top: 15px; color: red; font-weight: bold'>$diff</span>
                                  </div>
                                </div>";
          }

        }
      }
    } else {
      $output .= '<div class="text">메시지가 없습니다. <br> 메시지를 보내면 여기에 나타납니다.</div>';
    }

    // 상대방이 보낸 채팅 메시지를 chat.php에 들어가 봤다면 msg_read를 'yes'로 업데이트
    $readMsgUpdateQuery = "UPDATE messages SET `read` = 'yes' WHERE outgoing_msg_id = :outgoingId AND incoming_msg_id = :incomingId";
    $readMsgUpdateResult = $this->connection()->prepare($readMsgUpdateQuery);
    $readMsgUpdateResult->bindParam(':outgoingId', $incomingId);
    $readMsgUpdateResult->bindParam(':incomingId', $outgoingId);
    $readMsgUpdateResult->execute();

    echo $output;

  }

  public function logout($sessionId)
  {
    $selectSql = "SELECT * FROM users WHERE unique_id = :sessionId";
    $selectResult = $this->connection()->prepare($selectSql);
    $selectResult->bindParam(':sessionId', $sessionId);
    $selectResult->execute();

    $selectResultData = $selectResult->fetch(PDO::FETCH_ASSOC);

    $sessionUniqueIdArray = unserialize(base64_decode($selectResultData['session_unique_id']));

    if (count($sessionUniqueIdArray) >= 1) {
      array_splice($sessionUniqueIdArray, count($sessionUniqueIdArray) - 1);
    }

    $sessionUniqueIdEncode = base64_encode(serialize($sessionUniqueIdArray));


    $sql = "UPDATE users SET status = '비접속', session_unique_id = :sessionUniqueId WHERE unique_id = :sessionId";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':sessionId', $sessionId);
    $result->bindParam(':sessionUniqueId', $sessionUniqueIdEncode);
    $result->execute();

    $row_count = $result->rowCount();

    if ($row_count == 1) {
      return "업데이트 성공";
    } else {
      return "업데이트 실패";
    }
  }

  public function getUserDataById($unique_id)
  {
    $sql = "SELECT * FROM users WHERE unique_id = :unique_id";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':unique_id', $unique_id);
    $result->execute();

    return $result->fetch(PDO::FETCH_ASSOC);
  }

  public function editUserData($name, $email, $password, $image, $uniqueId)
  {
    // 프로필 수정에서 변경한 사진이 있다면 업데이트 구문에 입력
    if ($image !== null) {

      $updateSql = "UPDATE users SET name = :name, email = :email, password = :password, img = :img, updated_at = CURRENT_TIMESTAMP WHERE unique_id = :uniqueId";
      $updateResult = $this->connection()->prepare($updateSql);
      $updateResult->bindParam(':name', $name);
      $updateResult->bindParam(':email', $email);
      $updateResult->bindParam(':password', $password);
      $updateResult->bindParam(':img', $image);
      $updateResult->bindParam(':uniqueId', $uniqueId);

      $updateResultRow = $updateResult->execute();

      if ($updateResultRow == 1) {
        echo "프로필 수정 성공";
        exit;
      } else {
        echo "프로필 수정이 되지 않았습니다. 뭔가 문제가 있습니다.";
        exit;
      }

      // 프로필 수정에서 변경한 사진이 없다면
    } else {

      $sql = "SELECT * FROM users WHERE unique_id = :uniqueId";
      $result = $this->connection()->prepare($sql);
      $result->bindParam(':uniqueId', $uniqueId);
      $result->execute();

      $row = $result->fetch(PDO::FETCH_ASSOC);

      // 이름, 이메일, 패스워드 모두 다 기존 데이터와 같다면 업데이트가 의미가 없으므로 users.php 로 보내버림
      if ($name == $row['name'] && $email == $row['email'] && $password == $row['password']) {
        echo "이름, 이메일, 비밀번호 모두 변경된 값이 없습니다.";
        exit;

        // 이름, 이메일, 패스워드 뭐 하나라도 바뀐게 있다면 UPDATE문 실행
      } else {
        $updateSql = "UPDATE users SET name = :name, email = :email, password = :password, updated_at = CURRENT_TIMESTAMP WHERE unique_id = :uniqueId";
        $updateResult = $this->connection()->prepare($updateSql);
        $updateResult->bindParam(':name', $name);
        $updateResult->bindParam(':email', $email);
        $updateResult->bindParam(':password', $password);
        $updateResult->bindParam(':uniqueId', $uniqueId);

        $updateResultRow = $updateResult->execute();

        if ($updateResultRow == 1) {
          echo "프로필 수정 성공";
          exit;
        } else {
          echo "프로필 수정이 되지 않았습니다. 뭔가 문제가 있습니다.";
          exit;
        }
      }


    }
  }


  public function deleteMessage($deleteMessageId)
  {
    $sql = "DELETE FROM messages WHERE msg_id = :msg_id";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':msg_id', $deleteMessageId);
    $result->execute();
  }




  public function getPasswordRandomAuthCode()
  {
    $sql = "SELECT * FROM authcode ORDER BY RAND() LIMIT 1;";
    $result = $this->connection()->prepare($sql);
    $result->execute();

    $row_count = $result->rowCount();

    if ($row_count > 0) {
      $row = $result->fetch(PDO::FETCH_ASSOC);

      return $row['randomNumber'];
    }
  }


  public function getUsersDataByEmailAndPassword($email, $password)
  {
    global $log;

    try {
      $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
      $result = $this->connection()->prepare($sql);
      $result->bindParam(':email', $email);
      $result->bindParam(':password', $password);
      $result->execute();

      $row_count = $result->rowCount();

      if ($row_count > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row;
      }
    } catch (Exception $e) {
      $log->info("{ message: " . $e->getMessage() . ", location: 'db/Database.php' }");

      echo $e->getMessage();
    }

  }

  public function createKakaoUser($profileName, $profileImage, $profileUniqueId, $profileEmail)
  {
    $status = "접속";
    $type = "kakao";

    $sql = "INSERT INTO users (name, img, unique_id, email, status, session_unique_id, type) VALUES (:name, :img, :unique_id, :email, :status, :session_unique_id, :type)";

    $sessionUniqueIdEncode = base64_encode(serialize(array($profileUniqueId)));

    $result = $this->connection()->prepare($sql);
    $result->bindParam(':name', $profileName);
    $result->bindParam(':img', $profileImage);
    $result->bindParam(':unique_id', $profileUniqueId);
    $result->bindParam(':email', $profileEmail);
    $result->bindParam(':status', $status);
    $result->bindParam(':session_unique_id', $sessionUniqueIdEncode);
    $result->bindParam(':type', $type);
    $result->execute();

    $row_count = $result->rowCount();

    if ($row_count > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function kakaoLoginStatusUpdate($unique_id)
  {
    $status = '접속';

    $sql = "SELECT session_unique_id FROM users WHERE unique_id = :unique_id AND `type` = 'kakao' ";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':unique_id', $unique_id);
    $result->execute();

    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($row['session_unique_id'] == '') {
      $sessionUniqueIdEncode = base64_encode(serialize(array($unique_id)));

      $sql = "UPDATE users SET status = :status, session_unique_id = :session_unique_id WHERE unique_id = :unique_id";
      $result = $this->connection()->prepare($sql);
      $result->bindParam(':status', $status);
      $result->bindParam(':session_unique_id', $sessionUniqueIdEncode);
      $result->bindParam(':unique_id', $unique_id);
      $result->execute();
    } else {
      $sessionUniqueIdArray = unserialize(base64_decode($row['session_unique_id']));

      if (count($sessionUniqueIdArray) == 3) {
        echo "기존에 로그인한 기기가 3개를 초과했습니다.";
        exit;
      } else {
        array_push($sessionUniqueIdArray, $unique_id);

        $sessionUniqueIdEncode = base64_encode(serialize($sessionUniqueIdArray));

        $sql = "UPDATE users SET status = :status, session_unique_id = :session_unique_id WHERE unique_id = :unique_id";
        $result = $this->connection()->prepare($sql);
        $result->bindParam(':status', $status);
        $result->bindParam(':session_unique_id', $sessionUniqueIdEncode);
        $result->bindParam(':unique_id', $unique_id);
        $result->execute();
      }
    }


    // session_unique_id 컬럼에 데이터가 하나도 없다면
    // 곧바로 $unique_id 입력
    // 기존 값이 있다면 기존값에 추가
    // session_unique_id 값이 3개가 들어있다면 로그인 불가 처리
    // 코드 작성하기

    // $friends = array("조상호", "김민재", "감성현");
    // $friendsSerialize = base64_encode(serialize($friends));
    // $name = unserialize(base64_decode($row['name']));



  }

  /**
   * 커넥션 끊기
   */
  public function connectionClose()
  {
    $this->conn = null;
  }
}
