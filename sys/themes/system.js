


var DCMS = {
    
};

DCMS.Event = {

    /**
   * Подписка на событие
   * Использование:
   *  menu.on('select', function(item) { ... }
  */
    on: function(eventName, handler) {
        if (!this._eventHandlers) this._eventHandlers = [];
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
        if (!handlers) return;
        for(var i=0; i<handlers.length; i++) {
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

DCMS.Ajax = function(settings){
    if (!settings)
        throw "Не заданы параметры запроса";
    
    var xhr = getXmlHttp();    
    xhr.open(settings.post?"POST":'GET', settings.url, true);
    xhr.onreadystatechange= function(){
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
    fields:['mail_new_count','friend_new_count'],
    errors: 0,
    time_last: 0,    
    id_user: null,    
    timeout: null,
    interval: 7,
    delay_update: function(sec){               
        var self = this;
        self.stop();        
        self.timeout = setTimeout(function(){
            self.update.call(self);
        }, sec * 1000);
    },    
    update: function(){
        var self = this;
        DCMS.Ajax({
            url: '/ajax/user.json.php?'+this.fields.join('&')+'&_='+Math.random(),
            callback: function(){
                self.onresult.apply(self,arguments);
            },
            error: function(){
                self.onerror.call(self);
            }
        })
    },    
    onerror: function(){
        this.errors ++;
        this.delay_update(30 * this.errors);
    },    
    onresult: function(data){
        DCMS.Event.trigger('user_update', JSON.parse(data));            
        this.errors = 0;
        this.delay_update(this.interval);
    },
    stop:function(){
        if (this.timeout)
            clearTimeout(this.timeout);
    }
};



function getXmlHttp(){
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
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}