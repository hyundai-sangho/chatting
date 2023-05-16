// users.php의 search input 창
const searchBar = document.querySelector('.users .search input');
// users.php의 search input 창 안의 텍스트
const searchText = document.querySelector('.users .search span');
// users.php의 검색 아이콘 버튼
const searchButton = document.querySelector('.users .search button');
// users.php의 사용자 목록 div 박스
const userList = document.querySelector('.users .users-list');

// 비동기로 사용자 목록을 계속 가져오는 것을 isStop 변수로 조정
let isStop = true;

// users.php 접속하면 실행되는 함수
let run = async () => {
	// 사용자 데이터 가져오기 전까지 로딩 이미지 화면에 출력
	userList.innerHTML = "<div style='text-align: center;'><img style='width: 200px; border-radius: 10px;' src='https://media1.giphy.com/media/KG4PMQ0jyimywxNt8i/giphy.gif?cid=ecf05e47sjwb5je4u2vmqy7ise7yg74au85dbxrbw2uujg5a&ep=v1_gifs_search&rid=giphy.gif&ct=g'></div>";

	// users.php에서 사용자 데이터 가져와 화면에 뿌려줌.
	await fetch('php/users.php')
		.then((response) => response.text())
		.then((data) => {
			userList.innerHTML = data;
		});
};

// 화면 로드시 실행
run();

// 검색어를 입력하면 php/search.php 에서 사용자 데이터를 가져와 화면에 뿌려줌.
let searchUser = async (searchTerm) => {
	await fetch('php/search.php', {
		method: 'POST',
		body: `searchTerm=${searchTerm}`,
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
	})
		.then((response) => response.text())
		.then((data) => {
			userList.innerHTML = data;
		});
};

// 검색 아이콘 클릭시
searchButton.addEventListener('click', () => {
	// 검색어 입력창 active 클래스를 붙여주며 화면에 보여줌.
	searchBar.classList.toggle('active');
	// 검색어 입력창에 포커스 됨.
	searchBar.focus();
	// 검색 아이콘에 active 클래스가 붙으면서 X 버튼을 화면에 보여줌.
	searchButton.classList.toggle('active');
	// 검색어 입력창에 글이 남아있다면 지워버림
	searchBar.value = '';
});

// 검색어 입력창 부분의 텍스트를 클릭하면
searchText.addEventListener('click', () => {
	// 검색어 입력창 active 클래스를 붙여주며 화면에 보여줌.
	searchBar.classList.toggle('active');
	// 검색어 입력창에 포커스 됨.
	searchBar.focus();
	// 검색 아이콘에 active 클래스가 붙으면서 X 버튼을 화면에 보여줌.
	searchButton.classList.toggle('active');
	// 검색어 입력창에 글이 남아있다면 지워버림
	searchBar.value = '';
});

// 검색어 입력창에서 글자를 작성했다면
searchBar.addEventListener('keyup', async (event) => {
	let searchTerm = searchBar.value;

	// 검색어 인풋창에 글이 있으면 active 클래스 추가
	// 글이 없으면 active 클래스 제거
	if (searchTerm != '') {
		searchBar.classList.add('active');
	} else {
		searchBar.classList.remove('active');
	}

	// 엔터키를 누르면 searchUser 함수 호출하면서 검색 내용 지워버림
	if (event.keyCode == 13) {
		await searchUser(searchTerm);

		searchBar.value = '';
	}
	// 엔터를 누르지 않고 검색어만 썼다면 searchUser 함수 호출
	// 검색 내용은 그대로 유지
	else {
		await searchUser(searchTerm);
	}
});

// 검색어 입력창에서 포커스가 가있으면 isStop 변수를 false로 바꿔
// 백그라운드에서 비동기로 가져오는 행동을 중지시킴.
searchBar.addEventListener('focus', () => {
	isStop = false;
});

// 검색어 입력창에서 포커스가 사라지면 다시 isStop 변수를 true로 바꿔
// 백그라운드에서 비동기로 가져오는 행동을 재시작
searchBar.addEventListener('focusout', () => {
	isStop = true;
});

// 500ms 초 단위로 백그라운드에서 비동기로
// 계속 users.php에 사용자 데이터 가져오기 요청을 함.
// 다른 사용자가 접속하면 사용자 리스트가 업데이트됨.
setInterval(async () => {
	if (isStop) {
		await fetch('php/users.php')
			.then((response) => response.text())
			.then((data) => {
				if (!searchBar.classList.contains('active')) {
					userList.innerHTML = data;
				}
			});
	}
}, 500);

function submitForm() {
	document.querySelector('#profileForms').submit();
}
