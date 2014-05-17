$(function () {
    var $user = $("#user"),
        $reg = $("#reg"),
        $login = $("#login"),
        $my_mail = $("#my_mail"),
        $my_friends = $("#my_friends"),
        $menu_user = $("#menu_user"),
        $icon_menu = $("#icon_menu");

    var ajax_timeout = 7000;
    var scope = {};

    $("#icon_menu, #container_overflow").on('click', function () {
        $("#container_overflow, #container_menu, #container_content").toggleClass('menu');
    });

    $(scope).on('newMessage', function () {
        if (window.navigator.vibrate)
            window.navigator.vibrate([100, 100]);
        var audio = document.querySelector("#audio_notify");
        audio.pause();
        audio.loop = false;
        audio.currentTime = 0;
        audio.play();
    });

    $(scope).on('userRefreshed', function (event, data) {
        if (data) {

            if (user.mail_new_count < data.mail_new_count)
                $(scope).trigger('newMessage');

            user = data;
        }

        $icon_menu.toggleClass('mail', !!+user.mail_new_count);
        $icon_menu.toggleClass('friends', !!+user.friend_new_count);

        if (user.id) {
            $user.text(user.login).show();
            $my_mail.text(translate.mail + (+user.mail_new_count ? ' +' + user.mail_new_count : '')).show();
            $my_mail.attr('href', +user.mail_new_count ? '/my.mail.php?only_unreaded' : '/my.mail.php')
            $my_friends.text(translate.friends + (+user.friend_new_count ? ' +' + user.friend_new_count : '')).show();
            $menu_user.text(translate.user_menu).show();
            $reg.hide();
            $login.hide();

            setTimeout(function () {
                $(scope).trigger('userRefresh');
            }, ajax_timeout);

        } else {
            $user.hide();
            $my_mail.hide();
            $my_friends.hide();
            $menu_user.hide();
            $reg.text(translate.reg).show();
            $login.text(translate.auth).show();
        }
    });

    $(scope).on('userRefresh', function () {
        $.get(user_ajax_url, user).success(function (data) {
            if (!data)
                return;
            ajax_timeout = 7000;
            $(scope).trigger('userRefreshed', data);
        }).error(function () {
            ajax_timeout = 60000;
            $(scope).trigger('userRefreshed');
        });
    });

    $(scope).trigger('userRefreshed');
});