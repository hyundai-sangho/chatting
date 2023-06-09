// 로그인 or 회원가입 화면 비밀번호 입력창에 있는 눈알 아이콘
const eyeIcon = document.querySelector('.fa-eye');
// 로그인 or 회원가입 화면 비밀번호 입력창
const passwordInput = document.querySelector('.password');

// 비밀번호에 있는 눈알 아이콘을 누르면
eyeIcon.addEventListener('click', () => {
	// 비밀번호 type 속성이 text일 때는 password로 바꾸고
	// 눈알 아이콘에 active 클래스를 추가
	// active 클래스 추가시 눈알 아이콘의 색깔이 변하면서
	// 눈알에 대각선으로 선이 그어진 이모티콘으로 변경
	if (passwordInput.getAttribute('type') === 'text') {
		passwordInput.setAttribute('type', 'password');
		eyeIcon.classList.add('active');
	}
	// 비밀번호 type 속성이 password일 때는 text로 바꾸고
	// 눈알 아이콘에 active 클래스를 제거
	else {
		passwordInput.setAttribute('type', 'text');
		eyeIcon.classList.remove('active');
	}
});
