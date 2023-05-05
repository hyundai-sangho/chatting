// 회원가입 창의 폼
const form = document.querySelector('.signup form');
// 회원가입 버튼
const signUpButton = form.querySelector('.button input');
// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');

// 회원가입 버튼 클릭 또는 입력창에서 엔터키를 눌렀을 시에
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지
	event.preventDefault();

	// php/signup.php 파일로 POST로 폼 데이터에 form 값들을 집어넣어 보내버림
	await fetch('php/signup.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			// "성공" 메시지가 날라오면 users.php로 보내버림
			if (data == '성공') {
				location.href = 'users.php';
			}
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			else {
				errorText.textContent = data;
				errorText.style.display = 'block';
			}
		});
});
