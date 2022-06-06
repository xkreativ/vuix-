let dataAttr = new URLSearchParams(window.location.search).get("id");

if (!window.WebSocket) {
	document.body.innerHTML = 'WebSocket в этом браузере не поддерживается.';
}

// создать подключение
var socket = new WebSocket("ws://localhost:8081");

// отправить сообщение из формы publish
document.forms.messagebody.onsubmit = function() {
  var outgoingMessage = this.message.value;

  sendAjaxForm("chat", "ajax_message", "../ajax/ajax_add_message.php");

  function sendAjaxForm(result_form, ajax_form, url) {
    if(dataAttr == undefined) {
      dataAttr = $('.dialogs__item')[0].getAttribute('data-dialogs');
    }
    if($("#message-write").val() != "") {
      $.ajax({
        url: url, //url страницы (action_ajax_form.php)
        type: "POST", //метод отправки
        dataType: "html", //формат данных
        data: $("#" + ajax_form).serialize() + "&dataAttr=" + dataAttr,
        success: function (response) {
          //Данные отправлены успешно
          data = $.parseJSON(response);
          let date = new Date(data.date_create);
          $("#chat").append(
            `
            <div class="message-container ${data.id_user == myId ? "my-message-container" : ""}" data-message="${data.message_id}">
            <div class="message my__message"}">
                  <div class="message__text">${data.content}</div>
                  <span class="message__date">${
                    setNullInHours(date) + ":" + setNullInMinutes(date)
                  }</span>
              </div>
              <span class="delete-message">
            <span class="iconify" data-icon="fluent:delete-16-filled"></span>
            </span>
              </div>
            `
          );
          $("#message-write").val("");
          chatToBottom();
        },
        error: function (response) {
          // Данные не отправлены
          $("#" + result_form).html("Ошибка. Данные не отправлены.");
        },
      });
    }
  
  }

  socket.send(JSON.stringify({
    message: outgoingMessage,
    dataAttr: dataAttr,
    myId: myId,
  }));
  return false;
};

// обработчик входящих сообщений
socket.onmessage = function(event) {
  let getUrlID = new URLSearchParams(window.location.search).get("id");
  var incomingMessage = event.data;
  comingParam = JSON.parse(incomingMessage);
  if (dataAttr == comingParam.dataAttr && myId != comingParam.myId) {
    showMessage(comingParam);
  } 

  if(getUrlID != comingParam.dataAttr) {
    for (let i = 0; i < dialogsItem.length; i++) {
      if(dialogsItem[i].getAttribute("data-dialogs") == comingParam.dataAttr && myId != comingParam.myId) {
        dialogsItem[i].style.background = "#FFF2CF";
        dialogsItem[i].style.border = "1px solid #FFDA75";
        dialogsItem[i].querySelector('.dialogs__text').innerHTML = comingParam.message.substring(0,50);
        let status = dialogsItem[i].querySelector('.count__status');
        if(!status) {
          let tag = document.createElement('span');
          tag.className = 'count__status';
          tag.innerHTML = 1; 
          dialogsItem[i].append(tag)
        }
        if(status) {
          status.innerHTML = Number(status.innerHTML) + 1;
        }
      }
    }
  }

  for (let i = 0; i < dialogsItem.length; i++) {
    if(dialogsItem[i].getAttribute("data-dialogs") == comingParam.dataAttr && myId != comingParam.myId) {
      dialogsItem[i].querySelector('.dialogs__text').innerHTML = comingParam.message.substring(0,50);
    }
  }

  
};

// показать сообщение в div#subscribe
function showMessage(message) {
  let date = new Date(Date.now());
    $("#chat").append(
      `
      <div class="message"}">
            <div class="message__text">${message.message}</div>
            <span class="message__date">${
              setNullInHours(date) + ":" + setNullInMinutes(date)
            }</span>
        </div>
      `
    );
    chatToBottom();
}
