jQuery(document).ready(function ($) {

    $(document).on('click', '#deactivate-export-wp-page-to-static-html', function (e) {
        e.preventDefault();

        var deactivateLink = $(this).attr('href');
        $('#wpptsh-feedback-modal').fadeIn();
        $('#wpptsh-backdrop').fadeIn();
        

        $('#wpptsh-cancel').on('click', function () {
            $('#wpptsh-feedback-modal').fadeOut();
            $('#wpptsh-backdrop').fadeOut();
        });

        $('#wpptsh-submit').on('click', function () {
            var reason = "";
            reason = $('input[name="deactivate_reason"]:checked').val();
            const message = $('#wpptsh-feedback-text').val();

            $.post(wpptshData.ajaxUrl, {
                action: 'wpptsh_save_deactivation_feedback',
                reason_key: reason,
                feedback: message
            }, function () {
                window.location.href = deactivateLink;
            });
        });
    });

    $(document).on('click', '.go-pro-btn', function (e) {
        e.preventDefault();
        var goProLink = $(this).attr('href');
        const url = new URL(goProLink);
        const ref = url.searchParams.get('ref');

        $.post(wpptshData.ajaxUrl, {
            action: 'wpptsh_click_go_pro',
            button: ref
        }, function () {
            window.location.href = goProLink;
        });
    });
});
