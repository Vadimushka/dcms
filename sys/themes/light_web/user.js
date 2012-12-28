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
function onTextareaBlur(){
//DCMS.Animation.style(textarea, 'height', '' , 300);
}

function onTextareaChange(){
    textarea = this;
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

function onTextareaBBcodeClickB(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[b]', '[/b]');
}
function onTextareaBBcodeClickU(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[u]', '[/u]');
}
function onTextareaBBcodeClickI(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[i]', '[/i]');
}
function onTextareaBBcodeClickBIG(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[big]', '[/big]');
}
function onTextareaBBcodeClickSMALL(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[small]', '[/small]');
}


function onTextareaBBcodeClickIMG(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[img]http://', '[/img]');
}

function onTextareaBBcodeClickPHP(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[php]', '[/php]');
}

function onTextareaBBcodeClickGRADIENT(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[gradient from="#bc009a" to="#008e21"]', '[/gradient]');
}

function onTextareaBBcodeClickHIDE(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[hide balls="0" group="0"]', '[/hide]');
}

function onTextareaBBcodeClickSPOILER(event){
    var textarea = this.parentNode.parentNode.lastChild;
    DCMS.Dom.inputInsert(textarea, '[spoiler title="'+LANG.bbcode_spoiler+'"]', '[/spoiler]');
}

function textareaModify(){
    var texareas = document.querySelectorAll('textarea');
    for (var i =0; i < texareas.length; i++){
        var textarea = texareas[i];
        Event.add(textarea, 'blur', onTextareaBlur);
        Event.add(textarea, ['keyup', 'paste', 'cut', 'change', 'focus', 'input'], onTextareaChange);
                
        var node_txt_wrapper = DCMS.Dom.create('div', 'textarea_wrapper', textarea.parentNode, textarea);        
        node_txt_wrapper.appendChild(textarea);
        var node_bbcode = DCMS.Dom.create('div', 'textarea_bbcode', node_txt_wrapper, textarea);
        var node_bb_b = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_b+'">B</span>', '', node_bbcode);
        Event.add(node_bb_b, 'click', onTextareaBBcodeClickB);
        var node_bb_i = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_i+'">I</span>', '', node_bbcode);
        Event.add(node_bb_i, 'click', onTextareaBBcodeClickI);
        var node_bb_u = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_u+'">U</span>', '', node_bbcode);
        Event.add(node_bb_u, 'click', onTextareaBBcodeClickU);
        
        var node_bb_big = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_big+'">BIG</span>', '', node_bbcode);
        Event.add(node_bb_big, 'click', onTextareaBBcodeClickBIG);
        var node_bb_small = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_small+'">small</span>', '', node_bbcode);
        Event.add(node_bb_small, 'click', onTextareaBBcodeClickSMALL);
        
        
        
        var node_bb_img = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_img+'">IMG</span>', '', node_bbcode);
        Event.add(node_bb_img, 'click', onTextareaBBcodeClickIMG);
        
        var node_bb_php = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_php+'">PHP</span>', '', node_bbcode);
        Event.add(node_bb_php, 'click', onTextareaBBcodeClickPHP);
        
        var node_bb_gradient = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_gradient+'"><font color="#BC009A">G</font><font color="#A4118A">R</font><font color="#8D237B">A</font><font color="#75356C">D</font><font color="#5E475D">I</font><font color="#46584E">E</font><font color="#2F6A3F">N</font><font color="#177C30">T</font></span>', '', node_bbcode);
        Event.add(node_bb_gradient, 'click', onTextareaBBcodeClickGRADIENT);
        
        
        
        var node_bb_spoiler = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_spoiler+'">SPOILER</span>', '', node_bbcode);
        Event.add(node_bb_spoiler, 'click', onTextareaBBcodeClickSPOILER);
        
        var node_bb_hide = DCMS.Dom.createFromHtml('<span title="'+LANG.bbcode_hide+'">HIDE</span>', '', node_bbcode);
        Event.add(node_bb_hide, 'click', onTextareaBBcodeClickHIDE);
    }  
}

ready(textareaModify);

function onSpoilerClick(){
    
    if (DCMS.Dom.classHas(this.parentNode, 'expanded')){
        DCMS.Dom.classRemove(this.parentNode, 'expanded');
        DCMS.Dom.classAdd(this.parentNode, 'collapsed');
        DCMS.Animation.style(this.nextSibling, 'height', '0');
    }else{
        DCMS.Dom.classRemove(this.parentNode, 'collapsed');
        DCMS.Dom.classAdd(this.parentNode, 'expanded');
        DCMS.Animation.style(this.nextSibling, 'height', '');
    }
    return false;
}

function spoilersModify(){
    var spoilers = document.querySelectorAll('.DCMS_spoiler_title');
    for (var i =0; i < spoilers.length; i++){
        var spoiler = spoilers[i];
        DCMS.Dom.classAdd(spoiler.parentNode, 'collapsed');
        DCMS.Dom.setStyle(spoiler.nextSibling, 'height', '0');
        Event.add(spoiler, 'click', onSpoilerClick);
    }    
}
ready(spoilersModify);

function listing_auto_update(listing_node, url){
    var ids = [];
    for (var i = 0; i < listing_node.children.length; i++){
        ids.push(listing_node.children[i].id);
    }
    DCMS.listing_update(url, ids,
        function(data){
            // success
            listing_update(listing_node, data);
            setTimeout(function(){
                // повтор
                listing_auto_update(listing_node, url);
            }, 7000);
        },
        function(){
            // error
            setTimeout(function(){
                // повтор
                listing_auto_update(listing_node, url);
            }, 30000);
        });
}

function listing_update(listing_node, data){
    var json = JSON.parse(data);    
    if (!json)
        return;
    // console.log(json);
    
    for(var i = 0; i < json.add.length; i++){
        var dom_before = false, dom_after = false;
        var add = json.add[i];
        if (add.after_id){
            dom_after = listing_node.querySelector('#'+add.after_id);
            dom_before = dom_after?dom_after.nextSibling:false;            
        }
        else if (!dom_before){
            dom_before = listing_node.children[0];
        //console.log(dom_before);
        }
        
        var dom = DCMS.Dom.createFromHtml(add.html, false, listing_node, dom_before);
        DCMS.Dom.setStyle(dom, 'opacity', '0');
        DCMS.Animation.style(dom, 'opacity', '1', 500);
    }
    
    for(i = 0; i < json.remove.length; i++){
        var remove = json.remove[i];
        var dom_remove = listing_node.querySelector('#'+remove);
        if (DCMS.isDom(dom_remove)){
            DCMS.Animation.style(dom_remove, 'opacity', '0', 500, function(){
                this.parentNode.removeChild(this);
            });
        }
    }
}