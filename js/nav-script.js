// Добавляем класс в выбранный элемент списка
let list = document.querySelectorAll('.list');
for(let i=0; i<list.length; i++) {
    list[i].addEventListener('click', function() {
        let j = 0;
        while(j < list.length) {
            list[j++].className = 'list';
        }
        list[i].className = 'list active';
    })  
}
// Добавляем класс в меню переключатель
let menuToggle = document.querySelector('.toggle');
let navigation = document.querySelector('.navigation');
let main = document.querySelector('main');
let header = document.querySelector('.header');

let navToggle = document.cookie.match(/nav-toggle=(.+?)(;|$)/);

if(!navToggle) {
    document.cookie = "nav-toggle=1"
}

menuToggle.addEventListener('click', function() {
    let navCookie = document.cookie.match(/nav-toggle=(.+?)(;|$)/);
    if(navCookie[1] == 1) {
        document.cookie = "nav-toggle=0";
        menuToggle.classList.remove('active');
        navigation.classList.remove('active');
    } else if(navCookie[1] == 0){
        document.cookie = "nav-toggle=1";
        menuToggle.classList.add('active');
        navigation.classList.add('active');
    }
});



let btnLogout = document.querySelector('#logout');
let logoutImg = document.querySelector('.user-logout-image');

if(logoutImg) {
    logoutImg.onclick = function() {
        btnLogout.classList.toggle('none');
    }
}
