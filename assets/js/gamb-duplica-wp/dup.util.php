<?php
/*! ============================================================================
*  UTIL NAMESPACE: All methods at the top of the gamb-duplica-wp Namespace
*  =========================================================================== */
defined("ABSPATH") or die("");
?>

<script>
gamb-duplica-wp.Util.ajaxProgress = null;

gamb-duplica-wp.Util.ajaxProgressShow = function () {
    if (gamb-duplica-wp.Util.ajaxProgress === null) {
        gamb-duplica-wp.Util.ajaxProgress = jQuery('#dup-ajax-loader')
    }
    gamb-duplica-wp.Util.ajaxProgress
        .stop(true, true)
        .css('display', 'block')
        .delay(1000)
        .animate({
            opacity: 1
        }, 500);
}

gamb-duplica-wp.Util.ajaxProgressHide = function () {
    if (gamb-duplica-wp.Util.ajaxProgress === null) {
        return;
    }
    gamb-duplica-wp.Util.ajaxProgress
        .stop(true, true)
        .delay(500)
        .animate({
            opacity: 0
        }, 300, function () {
            jQuery(this).css({
                'display': 'none'
            });
        });
}

gamb-duplica-wp.Util.ajaxWrapper = function (ajaxData, callbackSuccess, callbackFail, options = {}) {
    let opts  = jQuery.extend({
        showProgress: true,
        timeout: 30000
    }, options);

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        timeout: opts.timeout,
        dataType: "json",
        data: ajaxData,
        beforeSend: function( xhr ) {
            if (opts.showProgress) {
                gamb-duplica-wp.Util.ajaxProgressShow();
            }
        },
        success: function (result, textStatus, jqXHR) {
            var message = '';
            if (result.success) {
                if (typeof callbackSuccess === "function") {
                    try {
                        message = callbackSuccess(result, result.data, result.data.funcData, textStatus, jqXHR);
                    } catch (error) {
                        console.error(error);
                        DupPro.addAdminMessage(error.message, 'error');
                        message = '';
                    }
                } else {
                    message = '<?php echo esc_js(__('RESPONSE SUCCESS', 'gamb-duplica-wp')); ?>';
                }
                if (message != null && String(message).length) {
                    DupPro.addAdminMessage(message, 'notice');
                }
            } else {
                if (typeof callbackFail === "function") {
                    try {
                        message = callbackFail(result, result.data, result.data.funcData, textStatus, jqXHR);
                    } catch (error) {
                        console.error(error);
                        message = error.message;
                    }
                } else {
                    message = '<?php echo esc_js(__('RESPONSE ERROR!', 'gamb-duplica-wp')); ?>' + '<br><br>' + result.data.message;
                }
                if (message != null && String(message).length) {
                    DupPro.addAdminMessage(message, 'error');
                }
            }
        },
        error: function (result) {
            DupPro.addAdminMessage(
                <?php echo wp_json_encode(__('AJAX ERROR! <br> Ajax request error', 'gamb-duplica-wp')); ?>,
                'error'
            );
        },
        complete: function () {
            gamb-duplica-wp.Util.ajaxProgressHide();
        }
    });
};

/**
 * Get human size from bytes number.
 * Is size is -1 return unknown
 *
 * @param {size} int bytes size
 */
gamb-duplica-wp.Util.humanFileSize = function(size) {
    if (size < 0) {
        return "unknown";
    }
    else if (size == 0) {
        return "0";
    } else {
        var i = Math.floor(Math.log(size) / Math.log(1024));
        return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    }
};

gamb-duplica-wp.Util.isEmpty = function (val) {
    return (val === undefined || val == null || val.length <= 0) ? true : false;
};

gamb-duplica-wp.Util.toggleShow = function (selector, show = 'auto') {
    var element = jQuery(selector);
    if (show === 'auto') {
        show = !element.is(":visible");
    }

    if (show) {
        element.hide().removeClass('no-display');
        element.fadeIn();
    } else {
        element.fadeOut();
    }
};


gamb-duplica-wp.Util.dynamicFormSubmit = function (url, method, params) {
    var form = jQuery('<form>', {
        method: method,
        action: url
    });

    jQuery.each(params, function (key, value) {
        form.append(jQuery('<input>', {
            'type': 'hidden',
            'name': key,
            'value': value
        }));
    });

    jQuery("body").append(form);
    form.submit();
};
</script>
