function hasClass(ele, cls) {
    return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
}
function addClass(ele, cls) {
    if (!this.hasClass(ele, cls)) ele.className += " " + cls;
}
function removeClass(ele, cls) {
    if (hasClass(ele, cls)) {
        var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
        ele.className = ele.className.replace(reg, ' ');
    }
}


var DCMS = {
};

DCMS.Event = {
    /**
     * Подписка на событие
     * Использование:
     *  menu.on('select', function(item) { ... }
     */
    on: function(eventName, handler) {
        if (!this._eventHandlers)
            this._eventHandlers = [];
        if (!this._eventHandlers[eventName]) {
            this._eventHandlers[eventName] = [];
        }
        this._eventHandlers[eventName].push(handler);
    },
    /**
     * Прекращение подписки
     *  menu.off('select',  handler)
     */
    off: function(eventName, handler) {
        var handlers = this._eventHandlers[eventName];
        if (!handlers)
            return;
        for (var i = 0; i < handlers.length; i++) {
            if (handlers[i] == handler) {
                handlers.splice(i--, 1);
            }
        }
    },
    /**
     * Генерация события с передачей данных
     *  this.trigger('select', item);
     */
    trigger: function(eventName) {

        if (!this._eventHandlers[eventName]) {
            return; // обработчиков для события нет
        }

        // вызвать обработчики 
        var handlers = this._eventHandlers[eventName];
        for (var i = 0; i < handlers.length; i++) {
            handlers[i].apply(this, [].slice.call(arguments, 1));
        }

    }
}

DCMS.Ajax = function(settings) {
    if (!settings)
        throw "Не заданы параметры запроса";

    var xhr = getXmlHttp();
    xhr.open(settings.post ? "POST" : 'GET', settings.url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState != 4)
            return;
        if (xhr.status == 200) {
            if (settings.callback)
                settings.callback.call(this, xhr.responseText);
        } else {
            if (settings.error)
                settings.error.call(this, xhr.statusText);
        }
    }
    xhr.send(settings.post);
};


var DCMS_USER_UPDATE = {
    fields: ['mail_new_count', 'friend_new_count'],
    errors: 0,
    time_last: 0,
    id_user: null,
    timeout: null,
    interval: 7,
    delay_update: function(sec) {
        var self = this;
        self.stop();
        self.timeout = setTimeout(function() {
            self.update.call(self);
        }, sec * 1000);
    },
    update: function() {
        var self = this;
        DCMS.Ajax({
            url: '/ajax/user.json.php?' + this.fields.join('&') + '&_=' + Math.random(),
            callback: function() {
                self.onresult.apply(self, arguments);
            },
            error: function() {
                self.onerror.call(self);
            }
        })
    },
    onerror: function() {
        this.errors++;
        this.delay_update(30 * this.errors);
    },
    onresult: function(data) {
        DCMS.Event.trigger('user_update', JSON.parse(data));
        this.errors = 0;
        this.delay_update(this.interval);
    },
    stop: function() {
        if (this.timeout)
            clearTimeout(this.timeout);
    }
};



function getXmlHttp() {
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}



(function() {

    var root = (typeof exports == 'undefined' ? window : exports);

    var config = {
        // Ensure Content-Type is an image before trying to load @2x image
        // https://github.com/imulus/retinajs/pull/45)
        check_mime_type: true
    };



    root.Retina = Retina;

    function Retina() {
    }

    Retina.configure = function(options) {
        if (options == null)
            options = {};
        for (var prop in options)
            config[prop] = options[prop];
    };

    Retina.init = function(context) {
        if (context == null)
            context = root;

        var existing_onload = context.onload || new Function;

        context.onload = function() {
            var images = document.getElementsByTagName("img"), retinaImages = [], i, image;
            for (i = 0; i < images.length; i++) {
                image = images[i];
                retinaImages.push(new RetinaImage(image));
            }
            existing_onload();
        };
    };

    Retina.isRetina = function() {
        var mediaQuery = "(-webkit-min-device-pixel-ratio: 1.5),\
                      (min--moz-device-pixel-ratio: 1.5),\
                      (-o-min-device-pixel-ratio: 3/2),\
                      (min-resolution: 1.5dppx)";

        if (root.devicePixelRatio > 1)
            return true;

        if (root.matchMedia && root.matchMedia(mediaQuery).matches)
            return true;

        return false;
    };


    root.RetinaImagePath = RetinaImagePath;

    function RetinaImagePath(path) {
        this.path = path;
        this.at_2x_path = path.replace(/\.\w+$/, function(match) {
            return "@2x" + match;
        });
    }

    RetinaImagePath.confirmed_paths = [];

    RetinaImagePath.prototype.is_external = function() {
        return !!(this.path.match(/^https?\:/i) && !this.path.match('//' + document.domain))
    };

    RetinaImagePath.prototype.check_2x_variant = function(callback) {
        var http, that = this;
        if (this.is_external()) {
            return callback(false);
        } else if (this.at_2x_path in RetinaImagePath.confirmed_paths) {
            return callback(true);
        } else {
            http = new XMLHttpRequest;
            http.open('HEAD', this.at_2x_path);
            http.onreadystatechange = function() {
                if (http.readyState != 4) {
                    return callback(false);
                }

                if (http.status >= 200 && http.status <= 399) {
                    if (config.check_mime_type) {
                        var type = http.getResponseHeader('Content-Type');
                        if (type == null || !type.match(/^image/i)) {
                            return callback(false);
                        }
                    }

                    RetinaImagePath.confirmed_paths.push(that.at_2x_path);
                    return callback(true);
                } else {
                    return callback(false);
                }
            };
            http.send();
        }
    };



    function RetinaImage(el) {
        this.el = el;
        this.path = new RetinaImagePath(this.el.getAttribute('src'));
        var that = this;
        this.path.check_2x_variant(function(hasVariant) {
            if (hasVariant)
                that.swap();
        });
    }

    root.RetinaImage = RetinaImage;

    RetinaImage.prototype.swap = function(path) {
        if (typeof path == 'undefined')
            path = this.path.at_2x_path;

        var that = this;
        function load() {
            if (!that.el.complete) {
                setTimeout(load, 5);
            } else {
                that.el.setAttribute('width', that.el.offsetWidth);
                that.el.setAttribute('height', that.el.offsetHeight);
                that.el.setAttribute('src', path);
            }
        }
        load();
    };




    if (Retina.isRetina()) {
        Retina.init(root);
    }

})();