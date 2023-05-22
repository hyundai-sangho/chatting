<?php

// db/Database.php 파일 getAllData() 메소드에서 사용
// users 테이블의 전체 사용자의 데이터를 가져온 후에 while 문으로 돌려서
// 사용자 한 명 한 명을 $output 데이터에 집어넣은 후 출력
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $userUniqueId = $row['unique_id'];

  $database = new Database();
  $sql2 = "SELECT * FROM messages WHERE outgoing_msg_id = :unique_id ORDER BY msg_id DESC LIMIT 1";
  $stmt2 = $database->connection()->prepare($sql2);
  $stmt2->bindParam(':unique_id', $userUniqueId);
  $stmt2->execute();

  $row_count2 = $stmt2->rowCount();
  $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
  if ($row_count2 > 0) {
    $msg = $result2['msg'];

    // 메시지가 15 글자가 넘어가면 ...을 붙여준다.
    if (mb_strlen($result2['msg']) > 15) {
      $msg = mb_substr($result2['msg'], 0, 15, 'utf-8') . '...';
    }
  } else {
    $msg = "채팅 메시지가 없습니다.";
  }

  if ($row['status'] == "비접속") {
    $offline = "offline";
  } else {
    $offline = '';
  }

  // 현재 로그인한 사용자와 대화 상대자와의 대화 갯수
  $sql3 = "SELECT * FROM messages WHERE outgoing_msg_id = :outgoing_msg_id AND incoming_msg_id = :incoming_msg_id AND msg_read = 'no'";
  $stmt3 = $database->connection()->prepare($sql3);
  $stmt3->bindParam(':outgoing_msg_id', $userUniqueId);
  $stmt3->bindParam(':incoming_msg_id', $_SESSION['unique_id']);
  $stmt3->execute();

  $row_count3 = $stmt3->rowCount();

  if ($row_count3 > 0) {
    $row_count3 = "<span style='position:relative; left:-10px; border: 2px solid orange; border-radius: 30%; background-color: orange; color: white; padding: 5px; font-weight: bold;'>$row_count3</span>";
  } else {
    $row_count3 = '';
  }


  $output .= "<a href='chat.php?user_id=$userUniqueId'>
                        <div class='content'>
                          <img src='$row[img]' alt='프로필 사진'/>

                          <div class='details'>
                            <span>
                              $row[name]
                            </span>
                            <p> $msg </p>
                          </div>


                        </div>
                        <div class='status-dot $offline'>
                          $row_count3
                          <i class='fas fa-circle'></i>
                        </div>
                      </a>";

}


?>
