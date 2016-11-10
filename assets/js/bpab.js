jQuery(document).ready(function ($) {

    $(function () {
        var hideDelay = 0,
            hideTimer = null,
            x, // hack: variable for ajax object
            showDelay = 1000 * bpab_ajax_delay, // delay before the pop-up shows itself in ms
            action = bpab_action === 'click' ?
                'click' :
                'mouseover';

        var container = $('<div id="popupContainer"><table border="0" cellspacing="0" cellpadding="0" align="center" class="popupBubble"><tr><td class="corner topLeft"></td><td class="top"></td><td class="corner topRight"></td></tr><tr><td class="left">&nbsp;</td><td><div id="popupContent"></div></td><td class="right">&nbsp;</td></tr><tr><td class="corner bottomLeft">&nbsp;</td><td class="bottom">&nbsp;</td><td class="corner bottomRight"></td></tr></table></div>');

        $('body').append(container);

        $('.avatar')
            .live(action, function (e) {
                if (action === 'click') {
                    e.preventDefault();
                }

                // get user or group ID and the type itself
                var elem_var = String($(this).attr('rel')).split('_');
                var elemType = elem_var[0];
                var elemID = elem_var[1];
                if (!elemID) {
                    return;
                }

                if (hideTimer) {
                    clearTimeout(hideTimer);
                }

                var pos = $(this).offset();
                var width = $(this).width();

                $.data(this, 'timer', setTimeout(function () {
                    // hack start
                    // display the container before the ajax request
                    window.cd_ab_positionPopupContent(container, pos, width);

                    // populate the popup with a loader.gif
                    var loading = '<img src="' + bpab_ajax_image + '/ajax-loader.gif" alt="Loading" />';
                    $('div#popupContent').html(loading);

                    // check for the current ajax request and abort it if needed
                    if (x) {
                        x.abort();
                        //noinspection JSUnusedAssignment
                        x = null;
                    }
                    // hack end

                    x = $.ajax({
                        type: 'GET',
                        url: ajaxurl,
                        dataType: 'html',
                        data: {
                            ID: elemID,
                            type: elemType,
                            action: 'cd_ab_the_avatardata'
                        },
                        success: function (data) {
                            // Verify requested person since we could have multiple ajax requests out if the server is taking a while.
                            if (data.indexOf(elemType + '_' + elemID) > 0) {
                                $('div#popupContent').html(data);
                                window.cd_ab_positionPopupContent(container, pos, width);
                            }
                        }
                    });
                }, showDelay));
            })
            .live('mouseleave', function () {
                if (hideTimer) {
                    clearTimeout(hideTimer);
                }
                hideTimer = setTimeout(function () {
                    // hack: abort the ajax request
                    if (x) {
                        x.abort();
                        x = null;
                    }
                    container.css('display', 'none');
                }, hideDelay);
            });

        // Allow mouseover of details without hiding details
        $('#popupContainer')
            .mouseover(function () {
                if (hideTimer) {
                    clearTimeout(hideTimer);
                }
            })
            .mouseleave(function () {
                if (hideTimer) {
                    clearTimeout(hideTimer);
                }
                hideTimer = setTimeout(function () {
                    // hack: abort the ajax request;
                    if (x) {
                        x.abort();
                        x = null;
                    }
                    container.css('display', 'none');
                }, hideDelay);
            });

    });

    window.cd_ab_getClientWidth = function () {
        return document.compatMode == 'CSS1Compat' && !window.opera ?
            document.documentElement.clientWidth :
            document.body.clientWidth;
    };
    window.cd_ab_getClientHeight = function () {
        return document.compatMode == 'CSS1Compat' && !window.opera ?
            document.documentElement.clientHeight :
            document.body.clientHeight;
    };
    window.cd_ab_positionPopupContent = function (container, pos, width) {
        var right = window.cd_ab_getClientWidth() - pos.left - width;
        var boxWidth = $('div#popupContainer').width();
        if (boxWidth < right) {
            container.css({
                left: (pos.left + width) + 'px',
                top: pos.top - 5 + 'px'
            });
        } else if (( pos.left - boxWidth ) < 0) {
            container.css({
                left: '0px',
                top: pos.top - 5 + 'px'
            })
        } else {
            container.css({
                left: (pos.left - boxWidth) + 'px',
                top: pos.top - 5 + 'px'
            })
        }
        container.css('display', 'block');
    };
});
