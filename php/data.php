<?php

// db/Database.php 파일 getAllData() 메소드에서 사용
// users 테이블의 전체 사용자의 데이터를 가져온 후에 while 문으로 돌려서
// 사용자 한 명 한 명을 $output 데이터에 집어넣은 후 출력
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

  $database = new Database();
  $sql2 = "SELECT * FROM messages WHERE outgoing_msg_id = :unique_id ORDER BY msg_id DESC LIMIT 1";
  $stmt2 = $database->connection()->prepare($sql2);
  $stmt2->bindParam(':unique_id', $row['unique_id']);
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

  $output .= "<a href='chat.php?user_id=$row[unique_id]'>
                        <div class='content'>
                          <img src='assets/img/$row[img]' alt='' />
                          <div class='details'>
                            <span>
                              $row[name]
                            </span>
                            <p> $msg </p>
                          </div>
                        </div>
                        <div class='status-dot $offline'>
                          <i class='fas fa-circle'></i>
                        </div>
                      </a>";

}





?>
