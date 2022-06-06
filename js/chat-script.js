let dialogsId = new URLSearchParams(window.location.search).get("id");

let dialogsItem = document.querySelectorAll(".dialogs__item");

let dialogsUserImage, dialogsUserName, queryString;
let dialogs = document.querySelector(".dialogs__list");
dialogs.addEventListener("click", (e) => {
  let target = e.target.closest(".dialogs__item");
  if (target) {
    dialogsUserImage = target.firstElementChild.getAttribute("src");
    dialogsUserName = target.querySelector(".username").innerHTML;
    queryString = target.getAttribute("data-dialogs");
    $.ajax({ 
      url: '../ajax/ajax_message_status.php',
      method: 'POST',
      data: {'dataAttr' : queryString},
      beforeSend: function() {
      inProgress = true;}
      }).done(function(data){
      data = jQuery.parseJSON(data);
      target.style.background = null;
      target.style.border = null;
      });
      if(target.querySelector('.count__status')) {
        target.querySelector('.count__status').remove();
      }
  }
  let dialogsId = new URLSearchParams(window.location.search).get("id");
  if (queryString != dialogsId) {
    messagesAjax(queryString);
  }
  let pageUrl = "?id=" + queryString;
  window.history.pushState("", "", pageUrl);

  if ($(window).width() < 500){
    $(".main").scrollLeft(500);
}
});



messagesAjax(dialogsId);

function chatToBottom() {
  var objDiv = document.getElementById("chat__body");
  objDiv.scrollTop = objDiv.scrollHeight;
}

function getNameMonth(date) {
  let days = [
    "Января",
    "Февраля",
    "Марта",
    "Апреля",
    "Мая",
    "Июня",
    "Июля",
    "Августа",
    "Сентябя",
    "Октября",
    "Ноября",
    "Декабря",
  ];

  return days[date.getMonth()];
}

function setNullInHours(date) {
  return date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
}
function setNullInMinutes(date) {
  return date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
}


/* Получаем свой ID пользователя */
let myId = $(".shadow-data").attr("data-id");
/* Пишем AJAX запрос для получения сообщений пользователя по клику по диалогу*/
var inProgress = false;
function messagesAjax(dialogsId) {

  for (let i = 0; i < dialogsItem.length; i++) {
    let activeDialog = dialogsItem[i].getAttribute('data-dialogs');
    if(activeDialog == dialogsId) {
      dialogsItem[i].className = "dialogs__item active"
    }
  }

  // dialogsItem[0].style.background = null;
  // dialogsItem[0].style.border = null;

  if(dialogsId == undefined) {
    dialogsId = $('.dialogs__item')[0].getAttribute('data-dialogs');
    let pageUrl = "?id=" + dialogsId;
    window.history.pushState("", "", pageUrl);
    $.ajax({ 
      url: '../ajax/ajax_message_status.php',
      method: 'POST',
      data: {'dataAttr' : dialogsId},
      beforeSend: function() {
      inProgress = true;}
      }).done(function(data){
      data = jQuery.parseJSON(data);
      $('.dialogs__item')[0].className = "dialogs__item active"
      });
      if($('.dialogs__item')[0].querySelector('.count__status')) {
        $('.dialogs__item')[0].querySelector('.count__status').remove();
      }
  }

  let dialogUserImage = $(".dialog__user .user__preview img");
  let dialogUserName = $(".dialog__user .username");
  let loader = '<img src="../img/loader.gif" alt="" class="loader-gif">';


  dialogUserImage.attr("src", dialogsUserImage);
  dialogUserName.html(dialogsUserName);
  $("#chat").empty();

    $.ajax({
      url: "../ajax/ajax_chat.php",
      method: "GET",
      data: { dialogsId: dialogsId },
      beforeSend: function () {
        inProgress = true;
        $("#chat").append(loader);
      },
    }).done(function (data) {
      $('.loader-gif').remove();
      let prevElem;
      data = jQuery.parseJSON(data);
      if (data.length > 0) {
        $.each(data, function (index, data) {
          let date = new Date(data.date_create);
          let nowDate = new Date();
          function isToday(dateToday) {
            if(dateToday == nowDate.getDate()) {
              return "Сегодня";
            } else {
              return dateToday + ' ' + getNameMonth(date);
            }
          }
          $("#chat").append(`
          ${
            date.getDate() != prevElem
              ? '<div class="history--new-bar"> <span>' +
                isToday(date.getDate()) +
                "</span> </div>"
              : ""
          }
          <div class="message-container ${data.id_user == myId ? "my-message-container" : ""}" data-message="${data.message_id}">
            <div class="message ${data.id_user == myId ? "my__message" : ""}">
                <div class="message__text">
                    ${data.content}
                </div>
                <span class="message__date">
                ${setNullInHours(date) + ":" + setNullInMinutes(date)}
                </span>
            </div>
            <span class="delete-message">
            <span class="iconify" data-icon="fluent:delete-16-filled"></span>
            </span>
          </div>
          `);
          prevElem = date.getDate();
        });
        inProgress = false;

        chatToBottom();
      }
    });
}

for (let i = 0; i < dialogsItem.length; i++) {
  dialogsItem[i].addEventListener("click", function () {
    let j = 0;
    while (j < dialogsItem.length) {
      dialogsItem[j++].className = "dialogs__item";
    }
    dialogsItem[i].className = "dialogs__item active";
  });
}

window.onclick = function(e) {
  let target = e.target.closest(".my-message-container")
  let targetActive = e.target.closest(".my-message-container.active");
  if(target && !targetActive) {
    target.className = 'message-container my-message-container active';
  } else if (targetActive) {
    target.className = 'message-container my-message-container';
  }


  if(e.target.closest(".delete-message")) {
    let dataAttribute = target.getAttribute('data-message');
    $.ajax({ 
      url: '../ajax/ajax_delete_message.php',
      method: 'POST',
      data: {'dataAttr' : dataAttribute},
      beforeSend: function() {
      inProgress = true;}
      }).done(function(data){
        target.remove();
      });
      if(target.previousElementSibling.className == 'history--new-bar' && target.nextElementSibling) {
        if(target.nextElementSibling.className == 'history--new-bar') {
          target.previousElementSibling.remove();
        }
      } else if (target.previousElementSibling.className == 'history--new-bar' && !target.nextElementSibling) {
          target.previousElementSibling.remove();
      }
  }

  if(e.target.closest(".chat__action-btn")) {
    console.log('asdfasdf')
    $.ajax({ 
      url: '../ajax/ajax_delete_chat.php',
      method: 'POST',
      data: {'dataAttr' : dialogsId},
      beforeSend: function() {
      inProgress = true;}
      }).done(function(data){
        $("#chat").html('<div class="chat-remove"> <span><span class="iconify" data-icon="fluent:delete-24-filled"></span></span> Чат удалён</div>');
      });
  }

  if(e.target.closest('.back-dialogs')) {
    $(".main").scrollLeft(0);
  }
}

