<!-- login.php, index.php, chat.php 에 들어가는 헤더 부분 -->
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- 기본 파비콘 -->
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon">
    <!-- 기본 파비콘 -->

    <!-- pwa에서 사용하는 manifest.json 추가 -->
    <link rel="manifest" href="manifest.json" crossorigin="use-credentials" />
    <!-- pwa에서 사용하는 manifest.json 추가 -->

    <script type="module" defer>
      import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwaupdate';
      const el = document.createElement('pwa-update')
      document.body.appendChild(el)
    </script>

    <title>실시간 채팅</title>

    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
