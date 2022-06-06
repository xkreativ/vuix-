var searchParams = new URLSearchParams(window.location.search);
let section = searchParams.get("section");
let idUser = searchParams.get("id");

let friendsCategoryItem = document.querySelectorAll(".friends__category_item");

if (idUser != null) {
  friendsCategoryItem[0].parentElement.setAttribute(
    "href",
    "id=" + idUser + "&section=subscribers"
  );
  friendsCategoryItem[1].parentElement.setAttribute(
    "href",
    "id=" + idUser + "&section=subscriptions"
  );
}

for (let i = 0; i < friendsCategoryItem.length; i++) {
  friendsCategoryItem[i].parentElement.onclick = function (event) {
    event.preventDefault();
    var searchParams = new URLSearchParams(window.location.search);
    let helpParam = searchParams.get("section");
    queryString = friendsCategoryItem[i].parentElement.getAttribute("href");
    var pageUrl = "?" + queryString;
    window.history.pushState("", "", pageUrl);

    // Добавляем активный класс
    let j = 0;
    while (j < friendsCategoryItem.length) {
      friendsCategoryItem[j++].className = "friends__category_item";
    }
    friendsCategoryItem[i].className = "friends__category_item active";

    // Запускаем функцию
    var searchParams = new URLSearchParams(window.location.search);
    let section = searchParams.get("section");
    let idUser = searchParams.get("id");
    if (helpParam != section) {
      friendAjax(section, idUser);
    }
  };
}

for (let i = 0; i < friendsCategoryItem.length; i++) {
  friendsCategoryItem[i].className = "friends__category_item";
}

if(section == null) {
  friendsCategoryItem[0].className = "friends__category_item active";
}

for (let i = 0; i < friendsCategoryItem.length; i++) {
  switch (section) {
    case "subscribers":
      friendsCategoryItem[0].className = "friends__category_item active";
      break;
    case "subscriptions":
      friendsCategoryItem[1].className = "friends__category_item active";
      break;
  }
}

friendAjax(section, idUser);

var inProgress = false;
function friendAjax(section, id) {
  if (section == null || section == "subscribers") {
    $("#friends__list").empty();
    let avatar;
    $.ajax({
      url: "../ajax/ajax_friends.php",
      method: "GET",
      data: { section: "subscribers", id: id },
      beforeSend: function () {
        inProgress = true;
      },
    }).done(function (data) {
      data = jQuery.parseJSON(data);
      if (data.length > 0) {
        $.each(data, function (index, data) {
          data.avatar == "0"
            ? (avatar = "/resource/noavatar.jpg")
            : (avatar =
                "resource/users/" +
                data.id_user +
                "/avatar/" +
                data.avatar +
                "");
          $("#friends__list").append(
            `<div class="friend__item"><div class="friend__avatar"> <a href="${data.login}"> 
            <img src="${avatar}" alt=""> </a></div><div class="friend__info"><a href="${data.login}">
              <div class="friend__username">${data.name + ' ' +data.surname}</div></a><div class="friend__text">
              ${data.info}</div><div class='friend__actions'>
              <div class='friend__action' data-user='${data.id_user}'>Написать сообщение</div> 
              <div class='friend__action subscribe' data-user='${data.id_user}'>${data.yes ? 'отписаться' : 'подписаться'}</div></div></div> </div>`
          );
        });
        inProgress = false;
        let friendtitle = document.querySelector("#page__title_friends");
        friendtitle.innerHTML = "Подписчики - " + data.length;
      }
      if (data.length == 0) {
        $("#friends__list").append(
          '<div class="nofriends"><span class="iconify" data-icon="tabler:friends"></span><p>У вас пока что нет друзей, вы можете найти их с помощью поиска, или по советам справа</p></div>'
        );
      }
    });
  }
  if (section == "subscriptions") {
    $("#friends__list").empty();
    let avatar;
    $.ajax({
      url: "../ajax/ajax_friends.php",
      method: "GET",
      data: { section: "subscriptions", id: id },
      beforeSend: function () {
        inProgress = true;
      },
    }).done(function (data) {
      data = jQuery.parseJSON(data);
      if (data.length > 0) {
        $.each(data, function (index, data) {
          data.avatar == "0"
            ? (avatar = "/resource/noavatar.jpg")
            : (avatar =
                "resource/users/" +
                data.id_user +
                "/avatar/" +
                data.avatar +
                "");
          $("#friends__list").append(
            '<div class="friend__item"><div class="friend__avatar"> <a href="' +
              data.login +
              '"> <img src="' +
              avatar +
              '" alt=""> </a></div><div class="friend__info"><a href="' +
              data.login +
              '"><div class="friend__username">' +
              data.name +
              " " +
              data.surname +
              '</div></a><div class="friend__text">' +
              data.info +
              "</div><div class='friend__actions'><div class='friend__action' data-user='" + data.id_user
               +"'>Написать сообщение</div> <div class='friend__action subscribe' data-user='"+data.id_user+"'>отписаться</div></div></div> </div>"
          );
        });
        inProgress = false;
        let friendtitle = document.querySelector("#page__title_friends");
        friendtitle.innerHTML = "Подписки - " + data.length;
      }
      if (data.length == 0) {
        $("#friends__list").append(
          '<div class="nofriends"><span class="iconify" data-icon="tabler:friends"></span><p>Вы пока что ни на кого ни подписаны</p></div>'
        );
      }
    });
  }
}


$('#friends__list').click(function(event) {
  let target = event.target;
  if(target.className == 'friend__action') {
    let dataAttr = target.getAttribute('data-user');
    console.log(dataAttr);
    $.ajax({
            url: '../ajax/chat/ajax_create_new_chat.php',
            method: 'POST',
            data: {
                    dataAttr: dataAttr
            },
            beforeSend: function () {
                    inProgress = true;
            }
    }).done(function (data) {
            data = jQuery.parseJSON(data);
            window.location.href = "/messages?id=" + data[0].chat_id;
    })
  }
  if(target.className == 'friend__action subscribe') {
    let dataAttr = target.getAttribute('data-user');
    console.log(dataAttr);
    $.ajax({
            url: '../ajax/ajax_subscribe.php',
            method: 'POST',
            data: {
                    dataAttr: dataAttr
            },
            beforeSend: function () {
                    inProgress = true;
            }
    }).done(function (data) {
            data = jQuery.parseJSON(data);
           if(data) {
              target.innerHTML = 'подписаться';
           } else {
            target.innerHTML = 'отписаться';
           }
    })
  }
})
