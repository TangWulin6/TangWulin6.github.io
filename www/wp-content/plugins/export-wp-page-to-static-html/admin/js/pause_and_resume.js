(function ($) {
    'use strict';

    $(document).on("click", ".flat-button.pause", function () {
        var datas = {
            'action': 'rc_export_set_pause',
            'rc_nonce': rcewpp.nonce,
            'paused': 'paused',
        };

        $.ajax({
            url: rcewpp.ajax_url,
            data: datas,
            type: 'post',
            dataType: 'json',

            beforeSend: function () {

            },
            success: function (r) {
                if (r.success) {
                    if (r.paused){
                        $('#is_paused').val('true');
                        $('.flat-button.resume').show();
                        $('.flat-button.pause').hide();
                    }
                } else {
                    console.log('Something went wrong, please try again!');
                }

            }, error: function () {

            }
        });
    });

    $(document).on("click", ".flat-button.resume", function () {
        var datas = {
            'action': 'rc_export_set_pause',
            'rc_nonce': rcewpp.nonce,
            'paused': 'resumed',
        };

        $.ajax({
            url: rcewpp.ajax_url,
            data: datas,
            type: 'post',
            dataType: 'json',

            beforeSend: function () {

            },
            success: function (r) {
                if (r.success) {
                    if (!r.paused){
                        $('#is_paused').val('false');
                        $('.flat-button.pause').show();
                        $('.flat-button.resume').hide();
                        get_export_log_percentage(1000);
                    }


                } else {
                    console.log('Something went wrong, please try again!');
                }

            }, error: function () {

            }
        });
    });
})(jQuery);

