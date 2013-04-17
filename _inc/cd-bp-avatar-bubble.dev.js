jQuery(document).ready(function() {
    window.cd_ab_getClientWidth = function () { return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
    window.cd_ab_getClientHeight = function () { return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }
    window.cd_ab_positionPopupContent = function (container, pos, width) { 
      var right = window.cd_ab_getClientWidth() - pos.left - width;
      var boxWidth = jQuery('div#popupContainer').width();
      if ( boxWidth < right ) {
        container.css({
          left: (pos.left + width) + 'px',
          top: pos.top - 5 + 'px'
        });
      } else if ( ( pos.left - boxWidth ) < 0 ) {
        container.css({
          left: '0px',
          top: pos.top - 5 + 'px'
        })
      }else{
        container.css({
          left: (pos.left - boxWidth) + 'px',
          top: pos.top - 5 + 'px'
        })
      }
      container.css('display', 'block');
    }
});
