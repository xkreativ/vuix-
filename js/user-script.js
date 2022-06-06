const url = window.location.href;
var page = url.substring(url.lastIndexOf('/') + 1);
let idUser = document.querySelector('.shadow-data').getAttribute('data-id');

var inProgress = false;
var startFrom = 6;

let btn = $('#more-posts');

$('#more-posts').click(function () {
        $.ajax({
                url: '../ajax/ajax_user.php',
                method: 'POST',
                data: {
                        "startFrom": startFrom,
                        "page": page
                },
                beforeSend: function () {
                        inProgress = true;
                }
        }).done(function (data) {
                data = jQuery.parseJSON(data);
                if (data.length > 0) {
                        $.each(data, function (index, data) {
                                $("#posts__wrapper").append('<div class="user__post">' + '<img src="/resource/users/' + data.id_user + '/posts/' + data.photo + '"' + "data-link='" + data.id_post + "'" + ' alt= "Изображение">' + '</div>');
                        });
                        inProgress = false;
                        startFrom += 6;
                }
        })
});

/*---------------------------- СКРЫТИЕ КНОПКИ ЕСЛИ НЕТ ПОСТОВ ------------------------------------- */
let moreBtn = document.querySelector('.more');
if(moreBtn) {
        moreBtn.addEventListener('click', () => {
                let postCounter = document.querySelector('#post__counter .count');
                let postCount = document.querySelectorAll('.user__post');
        
                if ((postCount.length + 6) >= Number(postCounter.textContent)) {
                        moreBtn.classList.add('none');
                }
        });
}

/* СОЗДАНИЕ ЧАТА И РЕДИРЕКТ НА СТРАНИЦУ С СООБЩЕНИЯМИ */

$('#write__message_btn').click(function () {
        $.ajax({
                url: '../ajax/chat/ajax_create_new_chat.php',
                method: 'POST',
                data: {
                        dataAttr: idUser 
                },
                beforeSend: function () {
                        inProgress = true;
                }
        }).done(function (data) {
                data = jQuery.parseJSON(data);
                window.location.href = "/messages?id=" + data[0].chat_id;
        })
});