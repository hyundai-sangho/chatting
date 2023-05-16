// 비밀번호 찾기 화면 폼
const form = document.querySelector('form');

// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');

const findPasswordIcon = document.querySelector('#findPasswordIcon');

// 변경 완료 버튼 클릭 또는 입력창에서 엔터키를 눌렀을 시에 form에 submit 이벤트가 발생하면
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지해서 폼 값이 넘어가는 것을 막고
	event.preventDefault();

	// 비동기로
	// php/signup.php 파일로 POST로 폼 데이터에 form 값들을 집어넣어 보내버림
	await fetch('php/find-password.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			if (data === '디비에 해당하는 이름과 아이디가 없습니다.') {
				errorText.textContent = data;
				errorText.style.display = 'block';

				// 3초 딜레이 후에 find-password.php로 이동
				setTimeout(function () {
					location.href = 'find-password.php';
				}, 5000);
			} else if (data === '메시지를 보낼 수 없습니다.') {
				errorText.textContent = data;
				errorText.style.display = 'block';

				// 3초 딜레이 후에 find-password.php로 이동
				setTimeout(function () {
					location.href = 'find-password.php';
				}, 5000);
			} else {
				location.href = 'auth-code.php';
			}
		});
});
