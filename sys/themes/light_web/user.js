function user_update_mail_notice(){
/**
     * тут можно сделать дополнительное (звуковое) уведомление пользователя
     */
}

function user_update_mail(count){
    count = +count;
    var dom_mail = document.querySelector('#user_mail');
    
    
    
    if (USER.mail_new_count != count)
        user_update_mail_notice();
    USER.mail_new_count = count;
    
    if (USER.mail_new_count){
        dom_mail.querySelector('span').innerHTML = USER.mail_new_count; 
        DCMS.Dom.classRemove(dom_mail, 'hide');
    }else{
        DCMS.Dom.classAdd(dom_mail, 'hide');
    }
    
}
    
function user_update_friends(count){
    count = +count;
    
    var dom_friend = document.querySelector('#user_friend');
    USER.friend_new_count = count;
    
    if (USER.friend_new_count){
        dom_friend.querySelector('span').innerHTML = USER.friend_new_count;        
        DCMS.Dom.classRemove(dom_friend, 'hide');
    }else{
        DCMS.Dom.classAdd(dom_friend, 'hide');
    }
}
    
function user_update(data){
    if (data.id != USER.id){
        //console.log(USER, data);
        DCMS.UserUpdate.stop();
        //window.location.reload();
        return;
    }

    user_update_mail(data.mail_new_count);
    user_update_friends(data.friend_new_count);
}    

// подписываемся на событие поступления новых данных пользователя
DCMS.Event.on('user_update', user_update);

/**
 * Сворачиваем поле ввода до дефолтных размеров
 */
function textareaOnBlur(textarea){
    //DCMS.Animation.style(textarea, 'height', '' , 300);
}

function textareaOnChange(textarea){
    var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;
        
    var attributes_copy = ['width', 'font', 'padding'];    
    var testdiv = DCMS.Dom.createFromHtml('<div style="position: absolute; left: 99999px;word-break: break-all;white-space: pre-wrap" />', false, textarea.parentNode);
    
    var testvalue = textarea.value + "\n\n";
    
        
    if (hasInnerText)
        testdiv.innerText = testvalue;
    else
        testdiv.textContent = testvalue;
    
    for (var i =0; i < attributes_copy.length; i++){
        DCMS.Dom.setStyle(testdiv, attributes_copy[i], DCMS.Dom.getComputedValue(textarea, attributes_copy[i]));
    }    
    
    DCMS.Animation.style(textarea, 'height', DCMS.Dom.getComputedValue(testdiv, 'height') , 300);
    testdiv.parentNode.removeChild(testdiv);
}