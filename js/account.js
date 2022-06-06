var searchParams = new URLSearchParams(window.location.search); 
let searchPage = new URL(window.location);
let pageName = searchPage.pathname;
let userPhoto = searchParams.get("photo");
let userPost = searchParams.get("post");
let userId = document.querySelector('.shadow-data').getAttribute('data-id');

/* Текущее время */
var date = new Date();

/*---------------------------- ОТКРЫТИЕ МОДАЛЬНОГО ОКНА ПРИ КЛИКЕ НА ПОСТ ------------------------- */ 
let modal = document.querySelector('.modal'); 
let userPostsWrapper = document.querySelector('#posts__wrapper');
let modalImage = document.querySelector('.modal .modal__inner .modal__image img');

userPostsWrapper.onclick = function(event) {
        let target = event.target;
        if(target.tagName != 'IMG') return;
        showModal(target);
}

if(userPhoto != null) {
        let newElem = document.createElement("img");
        newElem.setAttribute('src', '/resource/users/'+ userId +'/posts/'+ b64_to_utf8(userPhoto));
        newElem.setAttribute('data-link', userPost);
        showModal(newElem);
}

function utf8_to_b64(str) {
        return window.btoa(unescape(encodeURIComponent(str)));
}

function b64_to_utf8(str) {
        return decodeURIComponent(escape(window.atob(str)));
}

$.fn.disableScroll = function() {
        window.oldScrollPos = $(window).scrollTop();
    
        $(window).on('scroll.scrolldisabler',function ( event ) {
           $(window).scrollTop( window.oldScrollPos );
           event.preventDefault();
        });
    };

    $.fn.enableScroll = function() {
        $(window).off('scroll.scrolldisabler');
    };

var inProgress = false;
let startFromComments = 0;

function showModal(elem) {
        modal.classList.add('active');
        // $("body").disableScroll();
        let dataAttr = elem.getAttribute('data-link');
        let src = elem.getAttribute('src');
        var filename = src.replace(/^.*[\\\/]/, '')
        codeBase = utf8_to_b64(filename);
        window.history.pushState("", "", "?photo=" + codeBase + "&post=" + dataAttr);
        modalImage.src = src;
        modalImage.setAttribute('data-link', dataAttr);

        let commentsBody = document.querySelector('#comments__body');
        
        commentsBody.addEventListener('click', function(event) {
                let target = event.target;
                if(target.className == 'comment__action delete__comment') {
                        let dataPost = target.getAttribute('data-post');
                                $.ajax({ 
                                        url: '../ajax/ajax_delete_comments.php',
                                        method: 'POST',
                                        data: {'dataPost' : dataPost},
                                        beforeSend: function() {
                                        inProgress = true;}
                                        }).done(function(data){
                                        data = jQuery.parseJSON(data);
                                        target.parentElement.parentElement.parentElement.remove();
                                });
                }
        })
        

        let postReactions = document.querySelector('.post__reactions');

        let counterLikes = document.querySelector('.counter__likes');

postReactions.onclick = function(event) {
        target = event.target;
        if(event.target.closest(".post__reactions-item.like")) {
                $.ajax({ 
                        url: '../ajax/ajax_likes.php',
                        method: 'POST',
                        data: {"dataAttr" : dataAttr,  "showonclick" : 1},
                        beforeSend: function() {
                        inProgress = true;}
                        }).done(function(data){
                        data = jQuery.parseJSON(data);
                        let btnLikeSvg = document.querySelector('.btnLike svg');
                        if(data == false) {
                                btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-filled');
                                btnLikeSvg.style.color = '#0A1465';
                                counterLikes.innerHTML = Number(counterLikes.innerHTML) + 1; 
                        } else {
                                btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-outlined');
                                btnLikeSvg.style.color = '#0A1465';
                                counterLikes.innerHTML = Number(counterLikes.innerHTML) - 1;
                        }
                });
        }
}
let loader = '<img src="../img/loader.gif" alt="" class="loader-gif">';

$.ajax({ 
        url: '../ajax/ajax_show_comments.php',
        method: 'POST',
        data: {"dataAttr" : dataAttr},
        beforeSend: function() {
        inProgress = true;
        $("#comments__body").empty();  
        $("#comments__body").append(loader);      
        }
        }).done(function(data){
            $('.loader-gif').remove();
        data = jQuery.parseJSON(data);
        let postCommentLength = document.querySelector('#post__reactions-count-comments');
        postCommentLength.textContent = data.length;
        if (data.length > 0) {
        $.each(data, function(index, data){
        let postDate = Date.parse(data.date) + 60 * 60 * 1000; 
        let deleteComment = data.id_user == data.me ? 'удалить комментарий' : ''; 
        let idBtnAction =  data.id_user == data.me ? 'delete__comment' : 'report__comment';
        $("#comments__body").append('<div class="comment"><div class="comment__avatar"> <a href="' + data.login +'"> <img src="resource/users/' + data.id_user + '/avatar/'+ data.avatar +'" alt="" class="user-avatar"> </a> </div><div class="comment__content"> <a href="'+ data.login +'"> <div class="comment__username">' + data.login +'</div> </a> <div class="comment__text">'+ data.text +'</div><div class="subcontent"><time class="timeago" title="">' + "Пользователь оставил комментарий: " + jQuery.timeago(postDate) + '</time><div class="comment__action ' + idBtnAction +'"'+ 'data-post="'+data.id+'">'+ deleteComment  + '</div></div></div></div>')
        });
        } else {
                $('#comments__body').append(`
                <div class="no-comments">
                                <p>Здесь нет ничего. Чтобы стать первым, напишите комментарий</p>
                            </div>
                `) 
        }
});
        $.ajax({ 
                url: '../ajax/ajax_show_usercomment.php',
                method: 'POST',
                data: {"dataAttr" : dataAttr},
                beforeSend: function() {
                        $("#user__comment").html('');
                    $("#user__date").html('');
                 }
                }).done(function(data){
                data = jQuery.parseJSON(data);
                $("#user__comment").append( data[0].text_post );
                $("#user__date").append( data[0].created_at );
                let counterLikes = document.querySelector('.counter__likes');
                counterLikes.innerHTML = data[0].count;
        });

        $.ajax({ 
                url: '../ajax/ajax_likes.php',
                method: 'POST',
                data: {"dataAttr" : dataAttr},
                beforeSend: function() {
                inProgress = true;}
                }).done(function(data){
                data = jQuery.parseJSON(data);
                let btnLikeSvg = document.querySelector('.btnLike svg')
                if(data != false) {
                        btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-filled');
                        btnLikeSvg.style.color = '#0A1465';
                } else {
                        btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-outlined');
                        btnLikeSvg.style.color = '#0A1465';
                }
        });

}

       
$("#comment__write_btn").click(
        function(){
                sendAjaxForm('comments__body', 'ajax_comment', '../ajax/ajax_add_comments.php');
                return false; 
        }
);


function sendAjaxForm(result_form, ajax_form, url) {
        let dataAttr = modalImage.getAttribute('data-link');
        let avatar;
$.ajax({
        url:     url, //url страницы (action_ajax_form.php)
        type:     "POST", //метод отправки
        dataType: "html", //формат данных
        data: $("#"+ajax_form).serialize() + '&dataAttr='+dataAttr,
        success: function(response) { //Данные отправлены успешно
                data = $.parseJSON(response);
                if($(".no-comments")) {
                        $(".no-comments").remove();
                }
                console.log(data);
                let deleteComment = data.id_user == data.me ? 'удалить комментарий' : 'пожаловаться'; 
                let idBtnAction =  data.id_user == data.me ? 'delete__comment' : 'report__comment';
                data.avatar == '0' ? avatar = '/resource/noavatar.jpg' : avatar = 'resource/users/' + data.id_user + '/avatar/'+ data.avatar +'';
                $("#comments__body").append('<div class="comment"><div class="comment__avatar"><img src="'+ avatar +'" alt="" class="user-avatar"></div><div class="comment__content"><div class="comment__username">'+ data.login +'</div><div class="comment__text">' + data.comment +'</div><div class="subcontent"><time class="timeago" title="">' +  jQuery.timeago(new Date()) + '</time><div class="comment__action ' + idBtnAction +'"'+ 'data-post="'+data.maxid+'">'+ deleteComment  + '</div></div></div></div>');
                $('#comment-write').val('');
        },
        error: function(response) { // Данные не отправлены
        $('#'+result_form).html('Ошибка. Данные не отправлены.');
        }
        });
}

modal.onclick = function(event) {
        let target = event.target;
        let targetClose = event.target.closest(".close-modal");
        if(target.className == 'modal active' || targetClose) {
                modal.classList.remove('active');   
                $("body").enableScroll();
                const url = new URL(document.location);
                const searchParams = url.searchParams;
                searchParams.delete("photo"); // удалить параметр "test"
                searchParams.delete("post"); // удалить параметр "test"
                window.history.pushState({}, '', url.toString());
                 
        }
}

let publish = document.querySelector('.publish');
        
publish.onclick = function(event) {
        let target = event.target;
        if(target.className == 'user__post-delete') {
                let dataDelete = target.getAttribute('data-delete');
                        $.ajax({ 
                                url: '../ajax/ajax_delete_posts.php',
                                method: 'POST',
                                data: {'dataDelete' : dataDelete},
                                beforeSend: function() {
                                inProgress = true;}
                                }).done(function(data){
                                data = jQuery.parseJSON(data);
                                target.parentElement.remove();
                        });
        }
}

let deleteUser = document.querySelector('#delete-user');

if(deleteUser) {
        deleteUser.onclick = function(event) {
                let target = event.target;
                let dataDelete = target.getAttribute('data-idUser');
                $.ajax({ 
                        url: '../ajax/ajax_delete_user.php',
                        method: 'POST',
                        data: {'dataDelete' : dataDelete},
                        beforeSend: function() {
                        inProgress = true;}
                        }).done(function(data){
                        data = jQuery.parseJSON(data);
                        
                });
        }
}
