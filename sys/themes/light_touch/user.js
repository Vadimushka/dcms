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
        dom_mail.className = '';
    }else{
        dom_mail.className = 'hide';
    }
    
}
    
function user_update_friends(count){
    count = +count;
    
    var dom_friend = document.querySelector('#user_friend');
    USER.friend_new_count = count;
    
    if (USER.friend_new_count){
        dom_friend.querySelector('span').innerHTML = USER.friend_new_count;        
        dom_friend.className = '';
    }else{
        dom_friend.className = 'hide';
    }
}
    
function user_update(data){
    if (data.id != USER.id){
        console.log(USER, data);
        DCMS_USER_UPDATE.stop();
        //window.location.reload();
        return;
    }

    user_update_mail(data.mail_new_count);
    user_update_friends(data.friends_new_count);
}    

// подписываемся на событие поступления новых данных пользователя
DCMS.Event.on('user_update', user_update);