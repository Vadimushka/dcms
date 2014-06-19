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
        event.preventDefault();
    }).parent().addClass('collapsed');
});

angular.module('Dcms', ['monospaced.elastic', 'ngAnimate'])
    .directive('ngInitial', function () { // инициализация модели по значению в инпуте
        return {
            restrict: 'A',
            controller: [
                '$scope', '$element', '$attrs', '$parse', function ($scope, $element, $attrs, $parse) {
                    var getter, setter, val;
                    val = $attrs.ngInitial || $attrs.value || $element.val();
                    getter = $parse($attrs.ngModel);
                    setter = getter.assign;
                    setter($scope, val);
                }
            ]
        };
    })
    .config(function ($httpProvider) {    // [url]http://habrahabr.ru/post/181009/[/url]
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        $httpProvider.defaults.transformRequest = function (data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? $.param(data) : data;
        };
    })
    .controller('FormCtrl',
        ['$rootScope', '$scope', '$element', '$http', '$timeout', '$compile',
            function ($rootScope, $scope, $element, $http, $timeout, $compile) {
                var form = $scope.form = {
                    showSmiles: {},
                    smilesLoaded: false,
                    smilesContent: '',
                    msg: '',
                    err: '',
                    sending: false, // происходит отправка сообщения
                    values: {},
                    getSmilesShowed: function (id) {
                        return !!form.showSmiles[id];
                    },
                    setSmilesShowed: function (id, value) {
                        if (!form.smilesLoaded)
                            form.loadSmiles();
                        return form.showSmiles[id] = !!value;
                    },
                    onBBcodeClick: function (args) {
                        InputInsert(args.Textarea, args.Code.Prepend, args.Code.Append);
                        $scope.$broadcast('elastic:adjust'); // обновление высоты textarea
                    },
                    pasteSmile: function (smile, textarea_id) {
                        InputInsert(document.getElementById(textarea_id), '', ' ' + smile + ' ', true);
                        $scope.$broadcast('elastic:adjust'); // обновление высоты textarea
                    },
                    onSubmit: function (event, url) {
                        if (!url)
                            return;
                        event.preventDefault();
                        if (form.sending)
                            return;
                        form.sending = true;
                        var formNode = event.target;
                        var postData = {};
                        for (var i = 0; i < formNode.elements.length; i++) {
                            postData[formNode.elements[i].name ] = formNode.elements[i].value;
                        }

                        $http.post(url, postData)
                            .success(function ($data) {
                                form.sending = false;
                                if ($data.msg)
                                    form.showMessage($data.msg);

                                if ($data.err)
                                    form.showError($data.err);

                                for (var k in $data.form) {
                                    form.values[k] = $data.form[k];
                                }

                                $rootScope.$broadcast('dcms:form_sended', $element.attr('id')); // Уведомляем о том, что форма была отправлена. Это событие должен слушать листинг
                            })
                            .error(function () {
                                form.sending = false;
                                form.showError(translates.error);
                            });
                    },
                    showError: function (err) {
                        form.err = err;
                        $timeout(function () {
                            form.err = '';
                        }, 3000);
                    },
                    showMessage: function (msg) {
                        form.msg = msg;
                        $timeout(function () {
                            form.msg = '';
                        }, 3000);
                    }
                };

                form.loadSmiles = function () {
                    form.smilesContent = 'Загрузка';
                    $http.get('/ajax/smiles.json.php')
                        .success(function ($data) {
                            form.smiles = $data;
                            form.smilesLoaded = true;
                        })
                        .error(function () {
                            form.smilesContent = 'Не удалось загрузить смайлы';
                        });
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
                    {Text: 'HIDE', Title: translates.bbcode_hide, Prepend: '[hide group="0" balls="0"]', Append: '[/hide]'}
                ];

                var textareas = $element.find('textarea');
                for (var i = 0; i < textareas.length; i++) {
                    var textareaNode = textareas[i];
                    var taId = textareaNode.id;
                    var $wrapper = angular.element(textareaNode).parent();
                    var $bbcodes = $wrapper.find('.textarea_bbcode');
                    for (var ii = 0; ii < codes.length; ii++) {
                        var $el = angular.element('<span></span>');
                        $el.text(codes[ii].Text);
                        $el.attr('title', codes[ii].Title);
                        $el.on('click', angular.bind($scope, form.onBBcodeClick, {Code: codes[ii], Textarea: textareaNode}));
                        $bbcodes.append($el);
                    }

                    var $el = angular.element('<span class="smiles" ng-click="form.setSmilesShowed(\'' + taId + '\', !form.getSmilesShowed(\'' + taId + '\'))" style="float: right"></span>');
                    $el.text(translates.smiles);
                    $el.append('<div class="smiles_drop_menu" ng-show="form.getSmilesShowed(\'' + taId + '\')"><div class="smiles_drop_menu_container">' +
                        '<div ng-repeat="smile in form.smiles" ng-click="form.pasteSmile(smile.code, \'' + taId + '\')"><img ng-src="{{smile.image}}" alt="{{smile.title}}" /></div>' +
                        '</div></div>');
                    $compile($el)($scope);
                    $bbcodes.append($el);
                }
            }])
    .controller('ListingCtrl',
        ['$rootScope', '$scope', '$http', '$interval', '$element', '$animate',
            function ($rootScope, $scope, $http, $interval, $element, $animate) {
                var listing = $scope.listing = {
                    counter: 0, // счетчик запросов. необходим при принудительном обновлении данных, чтобы если ответ для более раннего запроса придет последним (маловероятно, но все же), то он не учитывался.
                    url: '',
                    id_form: '',
                    updating: false,
                    /**
                     * Обновление листинга
                     * @param {boolean} forcibly Принудительное обновление
                     */
                    update: function (forcibly) {
                        if (!listing.url)
                            return;
                        if (listing.updating && !forcibly)
                            return;

                        listing.updating = true;
                        var counter = ++listing.counter;

                        var els = $element.children();
                        var skip_ids = [];
                        for (var i = 0; i < els.length; i++) {
                            skip_ids.push(els[i].id);
                        }

                        $http.post(listing.url, {skip_ids: skip_ids.join(',')})
                            .success(function ($data) {
                                if (counter != listing.counter)
                                    return;
                                listing.updating = false;

                                if ($data.remove && $data.remove.length)
                                    for (var i = 0; i < $data.remove.length; i++) {
                                        $animate.leave($element.find('#' + $data.remove[i]));
                                    }

                                if ($data.add && $data.add.length) {
                                    for (var i = 0; i < $data.add.length; i++) {
                                        var after_id = $data.add[i].after_id;
                                        var $el = angular.element($data.add[i].html);
                                        $animate.enter($el, $element, after_id ? $element.children('#' + after_id) : null);
                                    }
                                    if (!forcibly)
                                        $rootScope.$broadcast('dcms:newMessage');
                                }
                            })
                            .error(function () {
                                if (counter != listing.counter)
                                    return;
                                listing.updating = false;
                            });
                    }
                };

                $rootScope.$on('dcms:form_sended', function (event, id_form) {
                    if (id_form !== listing.id_form)
                        return;
                    listing.update(true);
                });

                $interval(angular.bind(listing, listing.update, false), 7000);
            }])
    .controller('DcmsCtrl',
        ['$scope', '$http', '$timeout', '$rootScope',
            function ($scope, $http, $timeout, $rootScope) {
                var scope = {
                    online: true,
                    interval: null,
                    requesting: false,
                    user: {}, // данные пользователя
                    translates: translates, // из document.tpl
                    URL: encodeURI(window.location.pathname + window.location.search),// адрес текущей страницы
                    str: {
                        mail: translates.mail, // Почта +[count]
                        friends: translates.friends // Друзья +[count]
                    },
                    onNewMessage: function () {

                    }
                };

                scope.setUserData = function (data, sound) {
                    sound = typeof sound == "undefined" || sound;

                    if (!data || typeof data.id === "undefined")
                        return;

                    if (sound && data.mail_new_count > scope.user.mail_new_count)
                        $rootScope.$broadcast('dcms:newMessage');

                    scope.user = angular.extend(scope.user, data);

                    var cMail = +scope.user.mail_new_count;
                    var cFriends = +scope.user.friend_new_count;

                    scope.str.mail = scope.translates.mail + (cMail ? ' +' + cMail : '');
                    scope.str.friends = scope.translates.friends + (cFriends ? ' +' + cFriends : '');
                };

                scope.requestUserData = function () {
                    if (scope.requesting)
                        return;
                    scope.requesting = true;
                    $http.get('/ajax/user.json.php?' + Object.keys(scope.user).join('&'))
                        .success(function ($data) {
                            scope.online = true;
                            scope.setUserData($data);
                            scope.requesting = false;
                            $timeout(scope.requestUserData, +scope.user.id ? 7000 : 60000);
                        })
                        .error(function () {
                            scope.online = false;
                            scope.requesting = false;
                            $timeout(scope.requestUserData, 30000);
                        });
                };

                $rootScope.$on('dcms:newMessage', function () {
                    var audio = document.querySelector("#audio_notify");
                    audio.pause();
                    audio.loop = false;
                    audio.currentTime = 0;
                    audio.play();
                });

                scope.setUserData(user, false); // user из document.tpl.php
                scope.requestUserData();
                angular.extend($scope, scope);
            }]);

