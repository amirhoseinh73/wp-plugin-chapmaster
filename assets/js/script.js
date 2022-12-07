function doc_ready( fn ) {
    if ( document.readyState === "complete" || document.readyState === "interactive" )
        setTimeout( fn, 1 );
    else
        document.addEventListener( "DOMContentLoaded", fn );
}

doc_ready( () => {
    remove_alert_fix()
} );


function remove_alert_fix() {
    const alert = document.querySelector( ".alert-fixed" )
    if ( ! alert ) return

    alert.classList.add( "fadeOut" )
    setTimeout( () => {
        if ( ! alert ) return
        alert.remove()
    }, 10000 )
}