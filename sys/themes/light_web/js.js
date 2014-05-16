/**
 * Вставка текста по выделению
 * @param {HTMLInputElement} node
 * @param {String} Open Текст, вставляемый перед выделением
 * @param {String} Close Текст, вставляемый после выделения
 * @param {boolean = false} CursorEnd Флаг, указывающий на необходимость установки курсора после вставленного текста
 * @returns {boolean}
 */
function InputInsert(node, Open, Close, CursorEnd) {
    node.focus();
    if (window.attachEvent && navigator.userAgent.indexOf('Opera') === -1) { // IE
        var s = node.sel;
        if (s) {
            var l = s.text.length;
            s.text = Open + s.text + Close;
            s.moveEnd("character", -Close.length);
            s.moveStart("character", -l);
            s.select();
        }
    } else {
        var ss = node.scrollTop;
        var sel1 = node.value.substr(0, node.selectionStart);
        var sel2 = node.value.substr(node.selectionEnd);
        var sel = node.value.substr(node.selectionStart, node.selectionEnd - node.selectionStart);

        node.value = sel1 + Open + sel + Close + sel2;
        if (CursorEnd) {
            node.selectionStart = sel1.length + Open.length + sel.length + Close.length;
            node.selectionEnd = node.selectionStart;
        } else {
            node.selectionStart = sel1.length + Open.length;
            node.selectionEnd = node.selectionStart + sel.length;
        }
        node.scrollTop = ss;

    }
    return false;
}

$(function () {
    $('.DCMS_spoiler_title').on('click',function (event) {
        $(this).parent().toggleClass('collapsed');
    }).parent().addClass('collapsed');
});

function DcmsCtrl($scope, $http, $interval) {
    var scope = {
        user: {}, // данные пользователя
        translates: translates, // из document.tpl
        URL: encodeURI(window.location.pathname),// адрес текущей страницы
        str: {
            mail: translates.mail, // Почта +[count]
            friends: translates.friends // Друзья +[count]
        },
        onNewMessage: function () {
            var audio = document.querySelector("#audio_notify");
            audio.pause();
            audio.loop = false;
            audio.currentTime = 0;
            audio.play();
        }
    };

    scope.setUserData = function (data, sound) {
        sound = typeof sound == "undefined" || sound;

        if (sound && data.mail_new_count > scope.user.mail_new_count)
            scope.onNewMessage();

        scope.user = data;

        var cMail = +scope.user.mail_new_count;
        var cFriends = +scope.user.friend_new_count;

        scope.str.mail = scope.translates.mail + (cMail ? ' +' + cMail : '');
        scope.str.friends = scope.translates.friends + (cFriends ? ' +' + cFriends : '');
    };

    scope.requestUserData = function () {
        if (!+scope.user.group)
            return;
        $http.get('/ajax/user.json.php?' + Object.keys(scope.user).join('&'))
            .success(function ($data) {
                scope.setUserData($data);
            })
            .error(function () {

            });
    };

    scope.setUserData(user, false); // из document.tpl
    $interval(scope.requestUserData, 7000);
    angular.extend($scope, scope);
};

angular.module('Dcms', ['monospaced.elastic']).
    controller('FormCtrl',
        ['$scope', '$element',
            function ($scope, $element) {
                var form = $scope.form = {
                    values: {

                    },
                    onBBcodeClick: function (args, event) {
                        InputInsert(args.Textarea, args.Code.Prepend, args.Code.Append);
                        $scope.$broadcast('elastic:adjust')
                    },
                    onSubmit: function (event) {

                    }
                };

                var codes = [
                    {Text: 'B', Title: translates.bbcode_b, Prepend: '[b]', Append: '[/b]'},
                    {Text: 'I', Title: translates.bbcode_i, Prepend: '[i]', Append: '[/i]'},
                    {Text: 'U', Title: translates.bbcode_u, Prepend: '[u]', Append: '[/u]'},
                    {Text: 'BIG', Title: translates.bbcode_big, Prepend: '[big]', Append: '[/big]'},
                    {Text: 'Small', Title: translates.bbcode_small, Prepend: '[small]', Append: '[/small]'},
                    {Text: 'IMG', Title: translates.bbcode_img, Prepend: '[img]http://', Append: '[/img]'},
                    {Text: 'PHP', Title: translates.bbcode_php, Prepend: '[php]', Append: '[/php]'},
                    {Text: 'SPOILER', Title: translates.bbcode_spoiler, Prepend: '[spoiler title=""]', Append: '[/spoiler]'},
                    {Text: 'HIDE', Title: translates.bbcode_hide, Prepend: '[hide group="0" balls="0"]', Append: '[/hide]'},
                ];

                var textareas = $element.find('textarea');
                for (var i = 0; i < textareas.length; i++) {
                    var textareaNode = textareas[i];
                    var $wrapper = angular.element(textareaNode).parent();
                    var $bbcodes = $wrapper.find('.textarea_bbcode');
                    for (var ii = 0; ii < codes.length; ii++) {
                        var $el = angular.element('<span></span>');
                        $el.text(codes[ii].Text);
                        $el.attr('title', codes[ii].Title);
                        $el.on('click', angular.bind($scope, form.onBBcodeClick, {Code: codes[ii], Textarea: textareaNode}));
                        $bbcodes.append($el);
                    }
                }
            }]);