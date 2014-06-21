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

    $('form').each(function () {
        var $element = $(this);
        var url = $element.attr('data-ajax-url');
        if (!url)
            return;

        $element.on('submit', function (event) {
            event.preventDefault();

            var formNode = event.target;
            var postData = {};
            for (var i = 0; i < formNode.elements.length; i++) {
                postData[formNode.elements[i].name ] = formNode.elements[i].value;
            }

            $element.attr('disabled', 'disabled');

            $.post(url, postData)
                .success(function (data) {
                    //form.sending = false;
                    //if ($data.msg)
                    //    form.showMessage($data.msg);

                    //if ($data.err)
                    //    form.showError($data.err);

                    for (var i = 0; i < formNode.elements.length; i++) {
                        var name = formNode.elements[i].name;
                        if (typeof data.form[name] == "undefined")
                            continue;
                        formNode.elements[i].value = data.form[name];
                    }
                    $element.attr('disabled', false);
                    $(scope).trigger('form_submit', $element.attr('id')); // Уведомляем о том, что форма была отправлена. Это событие должен слушать листинг
                })
                .error(function () {
                    $element.attr('disabled', false);
                });
        });
    });

    $(".listing").each(function () {
        var $element = $(this);
        var id_form = $element.attr('data-form-id');
        var url = $element.attr('data-ajax-url');
        if (!url)
            return;
        var timeout;

        $(scope).on('form_submit', function (event, id_form_arg) {
            if (id_form_arg == id_form)
                refresh(true);
        });

        var refresh = function (forcibly) {
            clearTimeout(timeout);

            var skip_ids = [];
            $element.children().each(function () {
                skip_ids.push(this.id);
            });

            $.post(url, {skip_ids: skip_ids.join(',')})
                .success(function (data) {

                    if (data.remove && data.remove.length)
                        for (var i = 0; i < data.remove.length; i++) {
                            $('#' + data.remove[i]).remove();
                        }

                    if (data.add && data.add.length) {
                        for (var i = 0; i < data.add.length; i++) {
                            var after_id = data.add[i].after_id;
                            var $el = $(data.add[i].html).css('opacity', '0');
                            if (after_id)
                                $element.children('#' + after_id).after($el);
                            else
                                $el.prependTo($element);

                            $el.animate({opacity: 1}, 500);
                        }

                        if (!forcibly)
                            $(scope).trigger('newMessage');
                    }

                    timeout = setTimeout(refresh, ajax_timeout);
                })
                .error(function () {
                    timeout = setTimeout(refresh, 60000);
                });
        };

        timeout = setTimeout(refresh, ajax_timeout);
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
        $.get(user_ajax_url, user)
            .success(function (data) {
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