const form = document.querySelector('form');

form.addEventListener('submit', async (event) => {
	event.preventDefault();

	await fetch('user/verify-password.php')
		.then((response) => response.text())
		.then((data) => {
			if (data == '이름, 이메일 쿠키 삭제, 인증 코드 세션 삭제 성공') {
				location.href = 'login.php';
			}
		});
});
