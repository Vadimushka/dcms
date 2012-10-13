var USER = function(){
    this.count_friends = 0;
    this.count_mail = 0;
    this.id = 0;
    this.message_file_path = null;
    
    this.timeout = null;
};

/**
 * Обновление отображаемых данных пользователя
 */
USER.updateBar = function(animate){
    if (animate == undefined)
        animate = 'normal';

    var mail = $("#mail");    
    if (this.count_mail == 0){
        mail.hide(animate);        
    }else{
        mail.html(lang_mail +' +'+this.count_mail);
        mail.show(animate);
    }
        
    var friends = $("#friends");
    if (this.count_friends == 0){
        friends.hide(animate); 
    }else{
        friends.html(lang_friends +' +'+this.count_friends);
        friends.show(animate);
    }
};

/**
 * обновление внутренних данных пользователя при помощи входящего JSON объекта
 */
USER.update = function(json){
    var id = parseInt(json.id);
    if (id != this.id){
        // если изменилась авторизация
        window.location.reload();
        return;
    }
    
    // кол-во новых писем
    var mail = parseInt(json.mail_new_count);    
    if (!isNaN(mail)){
        if (mail > this.count_mail){
            sound(this.message_file_path);
        }            
        this.count_mail = mail;
    }
    
    // кол-во новых запросов дружбы
    var friends = parseInt(json.friend_new_count);            
    if (!isNaN(friends)){
        this.count_friends = friends;
    }
    
    
    this.updateBar();
    this.timeout = setTimeout($.proxy(this.getData, this), 3000);
};

/**
 * Получение данных пользователя с сервера с периодичностью 3 секунды
 */
USER.getData = function(){
    if (this.id == 0)
        return;
    
    $.ajax({
        url: "/ajax/user.json.php",          
        cache: false,
        data: 'mail_new_count&friend_new_count',
        dataType: 'json',
        success: $.proxy(this.update, this)
    });
};

/**
 * Остановка периодических запросов получения JSON данных пользователя
 */
USER.getDataStop = function(){
    clearTimeout(this.timeout);    
};




function smiles_load(smw,button){
    $(button).text(lang_smiles_load);
    $.ajax({
        url: "/ajax/smiles.json.php",       
        dataType: 'json',
        cache: true,
        success: function(json){
            $.each(json, function(i, val) {                 
                smw.append( '<span class="smile" title="'+val.title+'"><img src="' + val.image + '" alt="' + val.code + '" /></span>');
            });    
                        
            $('span[class=smile]').click(function () { 
                insert_text($(this).parents('div[class=textarea]').children('textarea').attr('id'),'',' '+$(this).children('img').attr('alt') ,true);
            });
            
            $(button).text(lang_smiles_hide);
            smw.show('normal');
        },
        error: function(){
            $(button).text(lang_smiles);
            err(lang_smiles_load_err);
        }        
    }); 

}


// отображаем панель со смайлами
function smiles_show(button){
    var smw = $(button).parent().children('div[class=smiles_window]');
    if (smw.html()){
        // если смайлы уже загружены, то просто отображаем их   
        $(button).text(lang_smiles_hide);
        smw.show('normal');
    } else{
        // загружаем смайлы
        smiles_load(smw,button);
    }
}

// скрываем панель со смайлами
function smiles_hide(button){
    $(button).text(lang_smiles_show);
    var smw = $(button).parent().children('div[class=smiles_window]');
    smw.hide('normal');
}


// звуковое уведомление
function sound(path){
    $.fn.soundPlay({
        url: path,
        playerId: 'embed_player',
        command: 'play'
    });
}

function bbcode(id,name){
    switch(name){
        case 'u':
            insert_text(id,'[u]','[/u]',false);
            break
        case 'i':
            insert_text(id,'[i]','[/i]',false);
            break
        case 'b':
            insert_text(id,'[b]','[/b]',false);
            break
        case 'small':
            insert_text(id,'[small]','[/small]',false);
            break
        case 'big':
            insert_text(id,'[big]','[/big]',false);
            break
        case 'php':
            insert_text(id,'[php]','[/php]',false);
            break      
        case 'spoiler':
            insert_text(id,'[spoiler title=""]','[/spoiler]',false);
            break   
        case 'hide':
            insert_text(id,'[hide group="1" balls="0"]','[/hide]',false);
            break  
    }       
} 


function insert_text(id, Open, Close, CursorEnd) {
    var doc = document.getElementById(id);
    doc.focus();
    if (window.attachEvent && navigator.userAgent.indexOf('Opera') === -1) {                                        
        var s = doc.sel;
        if(s){                                  
            var l = s.text.length;
            s.text = Open + s.text + Close;
            s.moveEnd("character", -Close.length);
            s.moveStart("character", -l);                                           
            s.select();                
        }
    } else {                                              
        var ss = doc.scrollTop;
        var sel1 = doc.value.substr(0, doc.selectionStart);
        var sel2 = doc.value.substr(doc.selectionEnd);
        var sel = doc.value.substr(doc.selectionStart, doc.selectionEnd - doc.selectionStart);  
        
        
        doc.value = sel1 + Open + sel + Close + sel2;
        if (CursorEnd){
            doc.selectionStart = sel1.length + Open.length + sel.length + Close.length;
            doc.selectionEnd = doc.selectionStart;
        }else{            
            doc.selectionStart = sel1.length + Open.length;
            doc.selectionEnd = doc.selectionStart + sel.length;            
        }
        doc.scrollTop = ss; 
                                                    
    }
    return false;
}

function msg(text){
    $("div[class=messages]").hide('normal');
    $("<div class='msg'></div>").clone().replaceAll("div[class=messages]");
    $("div[class=msg]").text(text);
    $("div[class=messages]").show('normal');
}

function err(text){
    $("div[class=messages]").hide('normal');
    $("<div class='err'></div>").clone().replaceAll("div[class=messages]");
    $("div[class=err]").text(text);
    $("div[class=messages]").show('normal');
}

var link = {
    moved: false,   
    onmousedown: function(node){
        $('a, .spoiler', node).mouseup(function(){
            return false;
        });
        this.moved = false;
    },
    onmouseup: function(url,event){
        if (event.button == 2)
            return;            
        if (!url)
            return;
        if (this.moved)
            return;
        location.href = url;
        this.moved = true;
    },
    onmousemove: function(){
        this.moved = true;
    }
};



$(document).ready(function(){
        
    // обработка кнопок BBcode
    $('div[class=bb_menu] > span').click(function () { 
        bbcode($(this).parents('div[class=textarea]').children('textarea').attr('id'),$(this).attr('class'));
    });
   
   
    // обработка BBcode кнопки "Смайлы"
    $('div[class=bb_menu] > span[class=smiles]').toggle(function () {
        smiles_show(this);
    },function () {
        smiles_hide(this);
    });   
    
    // обработка спойлера
    $('div[class=spoiler] > span[class=spoiler_title]').toggle(function () {
        $(this).parent().children('div[class=spoiler_content]').show('normal');
    },function () {
        $(this).parent().children('div[class=spoiler_content]').hide('normal');
    });
    
    // создание блока под загружаемые смайлы
    $('div[class=bb_menu]').append('<div class="smiles_window"></div>');    
});