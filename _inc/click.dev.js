jQuery(document).ready(function() {
    
    jQuery(function() {
        var hideDelay = 0;
        var hideTimer = null;

        var container = jQuery('<div id="popupContainer"><table border="0" cellspacing="0" cellpadding="0" align="center" class="popupBubble"><tr><td class="corner topLeft"></td><td class="top"></td><td class="corner topRight"></td></tr><tr><td class="left">&nbsp;</td><td><div id="popupContent"></div></td><td class="right">&nbsp;</td></tr><tr><td class="corner bottomLeft">&nbsp;</td><td class="bottom">&nbsp;</td><td class="corner bottomRight"></td></tr></table></div>');

        jQuery('body').append(container);

        jQuery('.avatar').live('click', function(e) {

            e.preventDefault();
            
            var elem_var = String(jQuery(this).attr('rel')).split('_');
            var elemType = elem_var[0];
            var elemID = elem_var[1];
            if ( !elemID ) return;

            if (hideTimer) clearTimeout(hideTimer);
            
            var pos = jQuery(this).offset();
            var width = jQuery(this).width();

            jQuery('div#popupContainer').ajaxStart(function(){
                jQuery('div#popupContent').html('<img src="'+ajax_image+'/ajax-loader.gif" alt="Loading" />');   
                window.cd_ab_positionPopupContent(container, pos, width);
            });
            
            jQuery.ajax({
                type: 'GET',
                url: ajax_url,
                data: {
                    ID: elemID,
                    type: elemType,
                    action: 'cd_ab_the_avatardata'
                },
                success: function(data) {
                    // Get time dealy if any
                    var data = String(data).split('|~|');
                    //alert(data[0]);
                    if(data[0] > 0) {
                        var delay = data[0] * 1000;
                        setTimeout(function() {
                            // Show data
                            var text = jQuery(data[1]).html();
                            jQuery('div#popupContent').html(text);
                        }, delay);
                    }else{
                        // Show data
                        var text = jQuery(data[1]).html();
                        jQuery('div#popupContent').html(text);
                    }
                }
            });
  
        });

        jQuery('.avatar').live('mouseout', function() {
            if (hideTimer) clearTimeout(hideTimer);
            hideTimer = setTimeout(function() { container.css('display', 'none'); }, hideDelay);
        });

        // Allow mouseover of details without hiding details
        jQuery('#popupContainer').mouseover(function() {
            if (hideTimer) clearTimeout(hideTimer);
        });

        // Hide after mouseout
        jQuery('#popupContainer').mouseout(function() {
            if (hideTimer)  clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {container.css('display', 'none');}, hideDelay);
        });
        
    });

});
