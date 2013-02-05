// #whats-new bar
$(function(){
    $("body").mousedown(function(event) {
        if( event.which != 3 ){
            console.log( $( event.toElement).attr( "id" ) );
            if( $("#whats-new").attr( "display" ) != "none" ){

                if( $( event.toElement ).attr( "id" ) == "whats-new" || $( event.toElement).parents( "#whats-new").length > 0 ){
                    return event;
                }
                $("#whats-new").slideUp();
                return event;
            }
            return event;
        }
        $("#whats-new").slideDown();
        return event;
    });
});