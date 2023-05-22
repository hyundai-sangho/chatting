// 비밀번호 찾기 화면 폼
const form = document.querySelector('form');

// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');

const findPasswordIcon = document.querySelector('#findPasswordIcon');

const findPasswordName = document.querySelector('#findPasswordName');
const findPasswordEmail = document.querySelector('#findPasswordEmail');

// 변경 완료 버튼 클릭 또는 입력창에서 엔터키를 눌렀을 시에 form에 submit 이벤트가 발생하면
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지해서 폼 값이 넘어가는 것을 막고
	event.preventDefault();

	let regex = new RegExp('[a-z0-9]+@[a-z]+.[a-z]{2,3}');

	// 이메일 인풋창의 값이 이메일 형식이 맞을 때에만 인증 작업 시작
	if (regex.test(findPasswordEmail.value)) {
		// 비동기로
		// user/signup.php 파일로 POST로 폼 데이터에 form 값들을 집어넣어 보내버림
		await fetch('user/find-password.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				findPasswordName: findPasswordName.value,
				findPasswordEmail: findPasswordEmail.value,
			}),
		})
			.then((response) => response.text())
			.then((data) => {
				// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
				if (data === '입력하신 이름과 이메일을 다시 확인해 주세요.') {
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
					}, 3000);
				} else {
					location.href = 'auth-code.php';
				}
			});
	} else {
		alert('이메일을 형식에 맞게 입력하고 인증 버튼을 눌러주세요.');
	}
});
