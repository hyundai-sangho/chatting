<?php

/* php dotenv 사용을 위해 vendor 폴더 내부의 autoload.php require 함. */
require_once "$_SERVER[DOCUMENT_ROOT]/chatting/vendor/autoload.php";

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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
 * users.php에서 user_id를 Get으로 보낸 값을 토대로
 * 데이터를 가져와 프로필 사진, 이름, 활성 상태를 표시
 * return $resultData
 *
 * 3. getDataBySessionId($sessionId) 메소드
 * 회원가입 혹은 로그인 시에 생성되는 세션 아이디를 토대로
 * 데이터를 가져와 프로필 사진, 이름, 활성 상태를 표시
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
 * 9. connectionClose() 메소드
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
   * users.php에서 user_id를 Get으로 보낸 값을 토대로
   * 데이터를 가져와 프로필 사진, 이름, 활성 상태를 표시
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
   * 데이터를 가져와 프로필 사진, 이름, 활성 상태를 표시
   */
  public function getDataBySessionId($sessionId)
  {
    $sql = "SELECT * FROM users WHERE unique_id = :sessionId";
    $stmt = $this->connection()->prepare($sql);
    $stmt->bindParam(':sessionId', $sessionId);
    $stmt->execute();

    $row_count = $stmt->rowCount();

    if ($row_count > 0) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // users.php 에서 로그인한 사용자의 이름, 사진, 활성 상태를 표시
      echo "<div class='content'>
              <img src='assets/img/$result[img]' alt='' />
              <div class='details'>
                <span>
                  $result[name]
                </span>
              <p>
                $result[status]
              </p>
              </div>
            </div>";
    }
  }

  /**
   * users 테이블의 전체 사용자 데이터를 가져와 화면에 보여줌.
   */
  public function getAllData()
  {
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
      $output .= '대화가 가능한 상대가 없네요.';
    } elseif ($row_count >= 1) {
      require_once '../php/data.php';
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

    // $email, $password 변수에 값이 없다면 "모든 입력 필드가 필요합니다." 출력
    if (!empty($email) && !empty($password)) {
      $selectUsersTableByEmailQuery = "SELECT * FROM users WHERE email = :email";
      $stmt = $this->connection()->prepare($selectUsersTableByEmailQuery);
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      $row_count = $stmt->rowCount();

      // $email에 해당하는 데이터가 users 디비 테이블에 있다면
      if ($row_count > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // 디비에 저장된 암호화된 비밀번호와 로그인 페이지에서 입력한 평문 비밀번호와 비교해 검사
        if (password_verify($password, $row['password']) || $password == $row['password']) {
          $selectUsersTableByEmailAndPasswordQuery = "SELECT * FROM users WHERE email = :email AND password = :password";
          $stmt2 = $this->connection()->prepare($selectUsersTableByEmailAndPasswordQuery);
          $stmt2->bindParam(':email', $email);
          $stmt2->bindParam(':password', $row['password']);
          $stmt2->execute();

          $row_count2 = $stmt2->rowCount();

          // $email, $password 변수에 해당하는 users 디비 테이블에 데이터가 있다면
          // 세션 unique_id 값에 디비에서 받아온 unique_id 값 삽입
          if ($row_count2 > 0) {
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $_SESSION['unique_id'] = $row2['unique_id'];

            $sql = "UPDATE users SET status = '접속' WHERE unique_id = :sessionId";
            $result = $this->connection()->prepare($sql);
            $result->bindParam(':sessionId', $_SESSION['unique_id']);
            $result->execute();

            echo "성공";
          } else {
            echo "이메일 또는 비밀번호가 일치하지 않습니다.";
          }

        } else {
          echo "디비 비밀번호와 입력하신 비밀번호가 안 맞습니다.";
        }

      } else {
        echo "이메일이 디비에 존재하지 않습니다.";
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
      require_once '../php/data.php';
    } elseif ($row_count == 0) {
      $output .= "검색어와 관련된 사용자가 없습니다.";
    }

    echo $output;
  }

  /**
   * 회원가입 창에서 넘어온 이름, 이메일, 비밀번호를 users 디비 테이블에 저장
   */
  public function signUp($name, $email, $password)
  {
    // $name 변수의 공백 제거
    $name = preg_replace("/\s+/", "", $name);

    // 비밀번호 암호화
    $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

    // 이름, 이메일, 비밀번호가 없다면 "모든 입력 필드는 필수입니다." 화면에 출력
    if (!empty($name) && !empty($email) && !empty($password)) {
      // 이메일이 이메일 형식이 아니라면 "이메일 형식이 아니네요." 화면에 출력
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $result = $this->connection()->prepare($sql);
        $result->bindParam(':email', $email);
        $result->execute();

        $row_count = $result->rowCount();

        // 회원가입 창에서 입력한 이메일과 같은 이메일이 디비에 존재한다면
        // "이미 존재하는 이메일입니다." 화면에 출력
        if ($row_count > 0) {
          echo "{$email}은 이미 존재하는 이메일입니다.";
        }
        // 같은 이메일이 없다면 디비에 저장
        else {
          // 회원 가입창에서 프로필 사진을 넣었다면
          if (isset($_FILES['image'])) {
            $img_name = $_FILES['image']['name']; // 사용자가 업로드한 이미지 이름 가져오기
            $tmp_name = $_FILES['image']['tmp_name']; // 사용자가 업로드한 이미지의 임시 파일명 가져오기

            // 이미지명을 이름과 확장자를 분리해서 $img_explode 배열에 삽입
            $img_explode = explode('.', $img_name);
            // $img_ext 변수명에 이미지 확장자만 추출해서 삽입
            $img_ext = end($img_explode);

            // $extensions 배열에 이미지로 받을 확장자의 종류를 삽입
            $extensions = ['png', 'jpeg', 'jpg', 'webp'];
            // $extensions 배열에 사용자가 업로드한 이미지의 확장자명이 포함된다면
            if (in_array($img_ext, $extensions) === true) {
              // 현재 시간 구하기
              $time = time();

              // 이미지 중복 방지를 위해 현재 시간과 이미지명을 합쳐서 $new_img_name 변수명에 값 저장
              $new_img_name = $time . '_' . $img_name;
              // 이미지를 assets/img 폴더 내에 $new_img_name 명으로 이미지 파일 저장
              if (move_uploaded_file($tmp_name, "../assets/img/$new_img_name")) {
                $status = "접속";

                // 랜덤 숫자 구하기
                $random_id = rand(time(), 10000000);

                $sql2 = "INSERT INTO users (unique_id, name, email, password, img, status ) VALUES (:random_id, :name, :email, :encrypted_password, :new_img_name, :status)";
                // $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, name, email, password, img, status ) VALUES ('{$random_id}', '{$name}', '{$email}', '{$encrypted_password}', '{$new_img_name}', '{$status}')");

                $result2 = $this->connection()->prepare($sql2);

                $result2->bindParam(':random_id', $random_id);
                $result2->bindParam(':name', $name);
                $result2->bindParam(':email', $email);
                $result2->bindParam(':encrypted_password', $encrypted_password);
                $result2->bindParam(':new_img_name', $new_img_name);
                $result2->bindParam(':status', $status);

                $result2->execute();

                if ($result2) {
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

                    echo "성공";
                  }
                }
              } else {
                echo "뭔가 잘못됐다.";
              }

            } else {
              echo "이미지 파일을 선택하세요. - jpeg, jpg, png";
            }

          } else {

          }
        }
      } else {
        echo "이메일 형식이 아니네요.";
      }
    } else {
      echo "모든 입력 필드는 필수입니다.";
    }
  }

  /**
   * 로그인 창에서 로그인 상태 유지를 위해 이메일 데이터를 바탕으로
   * users 디비 테이블에서 데이터 존재 유무를 확인
   */
  public function getDataByEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = :email";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':email', $email);
    $result->execute();

    return $result->fetch(PDO::FETCH_ASSOC);
  }

  public function getDataByMessageAndOutgoingIdAndIncomingId($message, $outgoingId, $incomingId)
  {
    if (!empty($message)) {
      try {
        $sql = "INSERT INTO messages (msg, outgoing_msg_id, incoming_msg_id) VALUES (:message, :outgoing_id, :incoming_id)";
        $result = $this->connection()->prepare($sql);
        $result->bindParam(':message', $message);
        $result->bindParam(':outgoing_id', $outgoingId);
        $result->bindParam(':incoming_id', $incomingId);
        $result->execute();
      } catch (PDOException $e) {
        echo "INSERT 쿼리 실패";
        die();
      }

    }
  }

  public function getMessagesDataById($outgoingId, $incomingId)
  {
    $sql = "SELECT * FROM messages
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = :outgoingId AND incoming_msg_id = :incomingId) OR (outgoing_msg_id = :incomingId AND incoming_msg_id = :outgoingId) ORDER BY msg_id";

    $result = $this->connection()->prepare($sql);
    $result->bindParam(':outgoingId', $outgoingId);
    $result->bindParam(':incomingId', $incomingId);
    $result->execute();

    $results = $result->fetchAll(PDO::FETCH_ASSOC);
    $row_count = $result->rowCount();

    $output = '';

    if ($row_count > 0) {

      foreach ($results as $row) {
        // 메시지 보낸 사람 = 나
        if ($row['outgoing_msg_id'] == $outgoingId) {
          $output .= "<div class='chat outgoing'>
                                <div class='details'>
                                  <p> $row[msg] </p>
                                </div>
                              </div>";
        } else { // 메시지 받는 사람 = 다른 사람
          $output .= "<div class='chat incoming'>
                                  <img src='assets/img/$row[img]' alt=' />
                                  <div class='details'>
                                    <p> $row[msg] </p>
                                  </div>
                                </div>";

        }
      }
    } else {
      $output .= '<div class="text">메시지가 없습니다. <br> 메시지를 보내면 여기에 나타납니다.</div>';
    }
    echo $output;

  }

  public function logout($sessionId)
  {
    $sql = "UPDATE users SET status = '비접속' WHERE unique_id = :sessionId";
    $result = $this->connection()->prepare($sql);
    $result->bindParam(':sessionId', $sessionId);
    $result->execute();

    $row_count = $result->rowCount();

    if ($row_count == 1) {
      return "업데이트 성공";
    } else {
      return "업데이트 실패";
    }
  }

  /**
   * 커넥션 끊기
   */
  public function connectionClose()
  {
    $this->conn = null;
  }
}
