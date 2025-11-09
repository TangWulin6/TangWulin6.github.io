
  var percentage = 0;
  var log_id = 0;
  var shouldContinue = true;
  let smart = 0;

  function get_export_log_percentage(intervalTime = 0) {
    shouldContinue = true;
    log_id = 0;
    smart = 0;
    //callAssetExporter(intervalTime, 'url', 2);
    callAssetExporter(intervalTime, 'css', 2);
    callAssetExporter(intervalTime, 'js', 2);
    makeLogAjaxCall(intervalTime);
  }

  function stop_export_log_percentage() {
      shouldContinue = false;
      return new Promise(function (resolve, reject) {
          try {
              // Any cleanup code can go here if needed
              resolve("Export log percentage stopped successfully.");
          } catch (error) {
              reject(error);
          }
      });
  }

function fireExport(url) {
    return jQuery.ajax({
        url: rcewpp.endpoint,
        type: 'POST',
        data: {
            token: rcewpp.token,
            url: url.url
        },
        success: function (resp) {
            console.log('Worker ok:', resp);
        },
        error: function (xhr) {
            console.error('Worker error:', xhr.responseText);
        }
    });
}



  function makeLogAjaxCall(intervalTime) {
    if (!shouldContinue) return;
    var exportId = $('.export_id').val();

    var datas = {
      'action': 'export_log_percentage',
      'rc_nonce': rcewpp.nonce,
      'id': log_id,
      'time': Date.now(),
      'exportId': exportId,
    };

    $.ajax({
      url: rcewpp.ajax_url,
      data: datas,
      type: 'post',
      dataType: 'json',
      cache: false,
      success: function (r) {
        if (r.success) {
          logWorkers(r);
        } else {
          console.log('Something went wrong, please try again!');
        }        
        
        // Wait intervalTime ms, then make next request (if allowed)
        if (shouldContinue) {
          setTimeout(function () {
            makeLogAjaxCall(intervalTime);
          }, intervalTime);
        }
        
      },
      error: function () {
        console.log('Something went wrong, please try again!');
      },
      complete: function () {

      }
    });
  }

  function callAssetExporter(intervalTime, type, limit) {
    if (!shouldContinue) return;

    var datas = {
        action: 'wpptsh_assets_exporter',
        rc_nonce: rcewpp.nonce,
        asset_type: type,
        limit: limit,
    };

    $.ajax({
      url: rcewpp.ajax_url,
      data: datas,
      type: 'post',
      dataType: 'json',
      cache: false,
      success: function (r) {
        if (r.success) {
          if (shouldContinue) {
            setTimeout(function () {
              callAssetExporter(intervalTime, type, limit);
            }, intervalTime);
          }

        }
      },
      error: function () {
        console.log('Something went wrong, please try again!');
      },
      complete: function () {
        // Wait intervalTime ms, then make next request (if allowed)

      }
    });
  }

  $(document).on("click", ".see_logs_in_details", function(e){
    e.preventDefault();
    $('.logs').show();
    $('.logs_list').prepend('<div class="log main_log loading"><span class="danger log_type">Loading...</span></div>');

      var datas = {
        'action': 'see_logs_in_details',
        'rc_nonce': rcewpp.nonce,
        'id': log_id,
      };

      $.ajax({
        url: rcewpp.ajax_url,
        data: datas,
        type: 'post',
        dataType: 'json',

        beforeSend: function(){

        },
        success: function(r){
          if(r.success) {
            if(r.logs.length){
              export_logs_process(r.logs, r);
              if(r.cancel_command){
                $('.log.cancel_command').remove();
                $('.logs_list').prepend('<div class="log main_log cancel_command" id="48"><span class="danger log_type">Export process has been canceled!</span></div>')
              }
            }
          } else {
            console.log('Something went wrong, please try again!');
          }
        },
        error: function(){
          console.log('Something went wrong, please try again!');
        }
      });
  });

  
  //     // Public API
  //     window.progressUI = {
  //       setProgress,               // progressUI.setProgress(55)
  //       completeCheckpoint,        // progressUI.completeCheckpoint('zip')
  //       setOnlineUpload,           // progressUI.setOnlineUpload(true|false)
  //       error(msg){ errorBox.style.display='block'; errorBox.textContent = msg || 'Error, failed to export files!'; },
  //       clearError(){ errorBox.style.display='none'; }
  //     };


  //     // init
  //     setProgress(0);

  // progressUI.setOnlineUpload(false);
// Single shared state across reinjections
document.addEventListener("DOMContentLoaded", () => {
  // Single shared state
  const progressState =
    globalThis.__exportProgressState ||
    (globalThis.__exportProgressState = { last: 0 });

  // Hoisted helper
  function clamp(n, min, max) {
    return Math.max(min, Math.min(n, max));
  }

  function nextSmartProgress(last, target, reported, rng = Math.random) {
    last   = Math.floor(clamp(last ?? 0,   0, 100));
    target = Math.floor(clamp(target ?? 0, 0, 100));

    if (reported != null && reported > last) {
      return Math.min(target, Math.floor(reported));
    }
    if (last >= target) return target;

    const gap = target - last;
    const p2 = gap >= 20 ? 0.35 : gap >= 10 ? 0.25 : gap >= 5 ? 0.15 : 0;
    const p1 = gap >= 5 ? 0.60 : 0.70;
    const r  = rng();
    const step = r < p2 ? 2 : r < p2 + p1 ? 1 : 0;

    return last + Math.min(step, gap);
  }

  const order = ['html','assets','zip','upload'];
  let onlineUpload = false;

  const totals = {
    exported: document.querySelector('.total_exported_files'),
    fetched:  document.querySelector('.total_fetched_files')
  };

  // Now these will no longer be null
  const cps = {
    html:   document.getElementById('cp-html'),
    assets: document.getElementById('cp-assets'),
    zip:    document.getElementById('cp-zip'),
    upload: document.getElementById('cp-upload')
  };

  function visibleKeys() {
    return order.filter(k => k !== 'upload' || onlineUpload);
  }

  function setProgress(p){
    const bar      = document.getElementById('bar');
    const barValue = document.getElementById('barValue');
    const srBar    = document.getElementById('srBar');

    const pct = clamp(p, 0, 100);
    if (bar)      bar.style.width = pct + '%';
    if (barValue) barValue.textContent = Math.round(pct) + '%';
    if (srBar)    srBar.setAttribute('aria-valuenow', String(Math.round(pct)));
  }
  window.setProgress = setProgress;

  function toggleComplete(key, done){
    const el = cps[key];
    if (!el) return;
    el.classList.toggle('is-complete', !!done);
  }
  window.toggleComplete = toggleComplete;

  function completeCheckpoint(key){ toggleComplete(key, true); }

  function setOnlineUpload(flag){
    onlineUpload = !!flag;
    if (cps.upload) cps.upload.hidden = !onlineUpload;
  }


  function logWorkers(r) {
    if (!r || r.error) return;

    insertLogs(r.logs);
    if (totals.exported) totals.exported.textContent = r.total_url_exported ?? 0;
    if (totals.fetched)  totals.fetched.textContent  = r.total_urls_log ?? 0;

    page_exporter(r.latest_urls_to_export);
    let percentage = 0;

    if (r.creating_html_process === 'running') {
      console.log("status: html running");
      const frac = (r.totalExportedUrlsCount || 0) / Math.max(1, r.totalUrlsToExport || 1);
      percentage = frac * 33;
      smart = nextSmartProgress(progressState.last, 33, percentage);
    }
    else if (r.creating_html_process === 'completed' && !r.are_all_assets_exported && r.creating_zip_status !== 'completed') {
      console.log("status: html completed not assets");
      toggleComplete('html', true);
      const frac = (r.total_url_exported || 0) / Math.max(1, r.total_urls_log || 1);
      percentage = 33 + frac * 33;
      smart = nextSmartProgress(progressState.last, 66, percentage);
    }
    
    else if (r.creating_html_process === 'completed' && r.are_all_assets_exported && r.creating_zip_status !== 'completed') {
      exporting_assets_completed(r);
      const frac = (r.total_pushed_file_to_zip || 0) / Math.max(1, r.total_zip_files || 1);
      percentage = 66 + frac * 34;
      smart = nextSmartProgress(progressState.last, 100, percentage);
    }
    else if (r.creating_zip_status === 'completed') {
      
      //if (!$('.toastr-success').length){
        toastr.success('Successfully exported!', {"positionClass": "toast-top-center", time: 50000});
      //}
      playNotification();
      toggleComplete('html', true);
      exporting_assets_completed(r);
      toggleComplete('zip', true);
      smart = 100;
      stop_export_log_percentage();
      completeExport(r);
    }

    progressState.last = smart;
    setProgress(smart);
  }

  window.logWorkers = logWorkers;
});

function page_exporter(urls) {
  if (urls == null || urls.length == 0) {
    return;
  }
  console.log('URLS: ',urls);
  urls.forEach(function(u){
      fireExport(u);
  });
}
function exporting_assets_completed(response) {
    toggleComplete('assets', true);
    $('.view_exported_file').removeClass("hide").attr('href', response.createdLastHtmlFile);
}

/*Stop interval by interval variable name*/
function StopInterval(intervalName) {
  //stop_export_log_percentage();
  clearInterval(window[intervalName]);
}

function completeExport(r){
  $('.main_settings_page .spinner_x').addClass('hide_spin');
  $('.cancel_rc_html_export_process').hide();
  $('.download-btn').removeClass('hide').attr('href', r.zipDownloadLink);
  $('.export_internal_page_to_html').removeAttr('disabled');
  $('.export_external_page_to_html').removeAttr('disabled');
}

function insertLogs(logs) {
  var logsHtml = '';

  // Reverse loop to place the last item first
  logs.slice().reverse().forEach(element => {
    logsHtml += '<div class="log log--' + element.type + '">' +
      addLog({
        type: element.type,
        path: element.path,
        comment: element.comment
      }).html() + '</div>';
  });

  $('#logsList').html(logsHtml);

  // Always scroll to the top after updating
  // setTimeout(function () {
  //   $('#logsList').animate({ scrollTop: 500 }, "fast");
  // }, 300);
}

const $logsList   = $('#logsList');
  const $logSearch  = $('#logSearch');
  const $filterBtns = $('.filter');
  const $autoScroll = $('#autoScroll');
  const $copyBtn    = $('#copyLogs');
  const $clearBtn   = $('#clearLogs');

  function now(){ const d=new Date(); return d.toLocaleTimeString(); }
  function el(tag, className, text){
    const $n = $('<'+tag+'>');
    if (className) $n.addClass(className);
    if (text != null) $n.text(text);
    return $n;
  }

  function addLog({type='info', path='', comment=''}){
    const $row   = el('div', 'log log--'+type);
    const $badge = el('span', 'type', type.replace(/^(.)/, m=>m.toUpperCase()));
    const $code  = el('code', 'path', path || '');
    const $cmt   = comment ? el('span','comment','— '+comment) : null;
    const $time  = el('span','time', now());

    $row.append($badge, $code);
    if ($cmt) $row.append($cmt);
    $row.append($time);

    if ($autoScroll.length && $autoScroll.prop('checked')) {
      $row[0].scrollIntoView({block:'end'});
    }

    return $row;
  }

  function clearLogs(){ $logsList.empty(); }

  function getAllLogsText(){
    return $logsList.find('.log').map(function(){
      const $row = $(this);
      const t  = $row.find('.type').text()  || '';
      const p  = $row.find('.path').text()  || '';
      const c  = $row.find('.comment').text() || '';
      const tm = $row.find('.time').text()  || '';
      return '['+tm+'] '+t+': '+p+(c?' '+c:'');
    }).get().join('');
  }

  function applyFilters(){
    const q = ($logSearch.val() || '').toLowerCase();
    const showErrors = $filterBtns.filter('[data-filter="errors"]').hasClass('is-active');

    $logsList.children().each(function(){
      const $row = $(this);
      const isErr = $row.hasClass('log--danger');
      const text  = $row.text().toLowerCase();
      const match = (!showErrors || isErr) && (!q || text.includes(q));
      $row.toggle(!!match);
    });
  }

  $logSearch.on('input', applyFilters);

  $filterBtns.on('click', function(){
    $filterBtns.removeClass('is-active');
    $(this).addClass('is-active');
    applyFilters();
  });

  $copyBtn.on('click', async function(){
    try {
      await navigator.clipboard.writeText(getAllLogsText());
      $copyBtn.text('Copied');
      setTimeout(()=> $copyBtn.text('Copy'), 1200);
    } catch(e){
      $copyBtn.text('Failed');
      setTimeout(()=> $copyBtn.text('Copy'), 1200);
    }
  });

  $clearBtn.on('click', function(){ clearLogs(); });

    

  /*Clear logs data with percentage*/
  function ClearExportLogsData() {
    $('.progress_').text(0);
    $('.total_').text(0);

    $('.progress-bar').css({'width': 0 + '%'});
    $('.progress-value').html(0 + '%');
    $('.download-btn').addClass('hide').attr('href', "");
    $('.creatingZipFileLogs').hide();
    $('.uploadingFilesToFtpLogs').hide();
    $('.logs').hide();
    $('.logs_list').html('');
    $('.view_exported_file').attr('href', '').addClass('hide');
    $('.error-notice').hide()
    $('#export_id').val("");
  }

  const notificationSound = new Audio(rcewpp.notification_sound_url); 
  notificationSound.preload = "auto";

  function playNotification() {
    notificationSound.currentTime = 0; // restart from beginning
    notificationSound.play()
      .catch(err => console.log("Audio play failed:", err));
  }