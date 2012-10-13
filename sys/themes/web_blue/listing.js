function sortable(url){
    $(function() {
        $( "div[class=listing]" ).sortable({
            items: 'div[class=sortable]',
            // cursor: 'move',
            stop: function(event, ui) {
                arr = new Array();
                $(this).children().each(function(){
                    arr.push(this.id);
                });
               
                $.ajax({
                    type: 'POST',
                    url: url,          
                    cache: false,
                    data: 'sortable='+arr,
                    dataType: 'json',
                    success: function(data){
                        if (data.result==1){                           
                            msg(data.description);
                        }else{
                            err(data.description);
                        }                    
                    }        
                }); 
            }       
        
        });    
    });
}
