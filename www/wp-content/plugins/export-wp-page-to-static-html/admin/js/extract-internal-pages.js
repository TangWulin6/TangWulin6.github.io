
$(document).on("click", ".export_internal_page_to_html", function(e){
    e.preventDefault();

    ClearExportLogsData();
    StopInterval('intrvar');
    var exportId = createRandomId(5);
    $('.export_id').val(exportId);

    var page_list = $('#tabs-1 .select_pages_to_export .pages_list .single_page');
    var replace_urls = $('#tabs-1 #replace_all_url').is(':checked') ? true : false;
    var receive_email = $('#tabs-1 #email_notification').is(':checked') ? true : false;
    var email_lists = $('#tabs-1 #receive_notification_email').val();

    var image_to_webp = $('#tabs-1 #image_to_webp').is(':checked') ? true : false;

    var skip_assets = $('#tabs-1 #skip_assets').is(':checked') ? true : false;
    var skip_stylesheets = $('#tabs-1 #skip_stylesheets').is(':checked') ? true : false;
    var skip_scripts = $('#tabs-1 #skip_scripts').is(':checked') ? true : false;
    var skip_images = $('#tabs-1 #skip_images').is(':checked') ? true : false;
    var skip_videos = $('#tabs-1 #skip_videos').is(':checked') ? true : false;
    var skip_audios = $('#tabs-1 #skip_audios').is(':checked') ? true : false;
    var skip_docs = $('#tabs-1 #skip_docs').is(':checked') ? true : false;
    var run_task_in_bg = $('#tabs-1 #run_task_in_bg').is(':checked') ? true : false;

    var alt_export = $('#tabs-1 #alt_export').is(':checked') ? true : false;

    var pages = $('#export_pages').val();

    console.log(pages)
    if (pages.length > 0) {

        $('.logs_list').html('<div class="log log--info"><span class="type">Loading</span><code class="path">Preparing exporter…</code><span class="time">--:--:--</span></div>');
        $('.progress').removeClass('completed');
        $('.see_logs_in_details').show();
        $('.htmlExportLogs').fadeIn();

        $(this).parent().find('.spinner_x').removeClass('hide_spin');
        $('.download-btn').addClass('hide');
        $('.export_failed.error').hide();
        $('.cancel_rc_html_export_process').show()
        $('#cancel_ftp_process').val('false');

        $(this).attr('disabled', 'disabled');


        var ftp = 'no';
        var path = '';
        if ($('#tabs-1 #upload_to_ftp').is(":checked")) {
            ftp = 'yes';

            if ($('#tabs-1 #ftp_path').val() !== "") {
                path = $('#ftp_path').val();
            }
        }
        var full_site = 'no';
        if ($('#full_site').is(":checked")) {
            full_site = 'yes';
        }

        var login_as = '';
        if ($('#login_as').val().length > 0) {
            login_as = $('#login_as').val();
        }

        var skip_assets_data = {};
        if(skip_assets){
            if(skip_stylesheets){
                skip_assets_data['stylesheets'] = true;
            }
            if(skip_scripts){
                skip_assets_data['scripts'] = true;
            }
            if(skip_images){
                skip_assets_data['images'] = true;
            }
            if(skip_videos){
                skip_assets_data['videos'] = true;
            }
            if(skip_videos){
                skip_assets_data['audios'] = true;
            }
            if(skip_docs){
                skip_assets_data['docs'] = true;
            }
        }

        var datas = {
            'action': 'rc_export_wp_page_to_static_html',
            'rc_nonce': rcewpp.nonce,
            'pages': pages,
            'replace_urls': replace_urls,
            'skip_assets': skip_assets_data,
            'image_to_webp': image_to_webp,
            'image_quality': $('#image_quality').val(),
            'full_site': full_site,
            'login_as': login_as,
            'ftp': ftp,
            'path': path,
            'run_task_in_bg': run_task_in_bg,
            'receive_email': receive_email,
            'email_lists': email_lists,
            'alt_export': alt_export,
            'exportId': exportId,
            'time': Date.now()
        };

        $.ajax({
            url: rcewpp.ajax_url,
            data: datas,
            type: 'post',
            //async: false,
            dataType: 'json',
            cache: false,

            beforeSend: function(){

                if (!run_task_in_bg){
                    setTimeout(function(){
                        $('.flat-button.pause').show();
                        get_export_log_percentage(2000);
                    }, 1000);
                }
            },
            success: function(r){
                if(r.success == 'true'){
                    if (run_task_in_bg){
                        setTimeout(function(){
                            $('.flat-button.pause').show();
                            get_export_log_percentage(1000);
                        }, 2000);
                    }

                } else {
                    console.log('Something went wrong, please try again!');
                }

            }, error: function(){

            }
        });
    }
    else{
        alert('Please select a page');
    }
});
  function ClearExportLogsData() {
    $('.progress_').text(0);
    $('.total_').text(0);

    $('.progress-bar').css({'width': 0 + '%'});
    $('.progress-value').html(0 + '%');
    $('.download-btn').addClass('hide').attr('href', "");
    $('.creatingZipFileLogs').hide();
    $('.uploadingFilesToFtpLogs').hide();
    //$('.logs').hide();
    $('.logs_list').html('');
    $('.view_exported_file').attr('href', '').addClass('hide');
    $('.error-notice').hide()
    $('#export_id').val("");
    $('#checkpoints .cp').removeClass("is-complete");
  }

