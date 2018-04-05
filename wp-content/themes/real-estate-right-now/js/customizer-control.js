(function( $ ) {
    
 
    
    wp.customize.bind( 'ready', function() {
        var customize = this;
        var customize = wp.customize;
        customize.previewer.bind( 'preview-edit', function( data ) {
            var data = JSON.parse( data );
            var control = customize.control( data.name );
             if (data.name == 'realestaterightnow_sidebar')
             {
               // var mypage = location.protocol+'//'+document.domain+'/wp-admin/widgets.php'
               // window.location.href = mypage;
                control =  customize.panel( 'widgets' );
               //  control =  customize.section( 'sidebar' );
             }
                 control.focus();
        } );
    } );
    
  
    
})( jQuery );