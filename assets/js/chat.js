// 인풋창 div 폼
const form = document.querySelector('.typing-area');
// 글 입력창
const inputField = form.querySelector('.input-field');
// 채팅 div 박스
const chatBox = document.querySelector('.chat-box');

// chatBox 채팅 오토 스크롤링 기본값 true로 설정
let isChatBoxScrolled = true;

/**
 * php/get-chat.php에 비동기로 데이터 요청
 */
const run = async () => {
	await fetch('php/get-chat.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			chatBox.innerHTML = data;
		});
};

// 기본 실행 함수
run();

// 마우스가 chatBox div 내에 들어온다면 채팅 오토 스크롤링 방지
chatBox.addEventListener('mouseenter', () => {
	isChatBoxScrolled = false;
});

// 마우스가 chatBox div 내에 들어온다면 채팅 오토 스크롤링 실행
chatBox.addEventListener('mouseleave', () => {
	isChatBoxScrolled = true;
});

form.addEventListener('submit', async (event) => {
	event.preventDefault();

	if (inputField.value !== '') {
		await fetch('php/insert-chat.php', {
			method: 'POST',
			body: new FormData(form),
		})
			.then((response) => response.text())
			.then((data) => {
				if (data == 'INSERT 쿼리 실패') {
					alert(data);
				}
			});

		// 인풋창에서 엔터나 메시지 보내기 아이콘 버튼을 눌렀다면 기존 인풋창의 입력값 제거
		inputField.value = '';

		// 인풋창에서 메시지 보내기 아이콘 버튼을 클릭했다면 날라간 포커스를 인풋창으로 다시 되돌림
		inputField.focus();
	}
});

setInterval(async () => {
	await fetch('php/get-chat.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			chatBox.innerHTML = data;

			if (isChatBoxScrolled) {
				scrollTopBottom();
			}
		});
}, 500);

/**
 * 채팅 메시지가 화면을 넘어가 스크롤이 되면 자동으로
 * 메시지가 화면에 보이도록 위쪽으로 올라오게 하는 함수
 */
function scrollTopBottom() {
	chatBox.scrollTop = chatBox.scrollHeight;

	console.log('chatBox.scrollTop: ' + chatBox.scrollTop);
	console.log('chatBox.scrollHeight: ' + chatBox.scrollHeight);
}
