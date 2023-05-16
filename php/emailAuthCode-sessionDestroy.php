<?php

// 세션 사용
session_start();

// 세션 authCode 변수 삭제
unset($_SESSION['authCode']);

// 세션 파일 삭제
session_destroy();

?>
