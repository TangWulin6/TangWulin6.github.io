var $ = jQuery;

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
      return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
      return null;
    }

    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object
    if (data.text.indexOf(params.term) > -1) {
      var modifiedData = $.extend({}, data, true);
      modifiedData.text += ' (matched)';

      // You can return modified objects from here
      // This includes matching the `children` how you want in nested data sets
      return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

$(document).on("select2:selecting", function(e){
	if ( $(e.target).is('#export_pages') ) {
		if (!$('#posts_list').length) {
			$('#export_pages').append('<option id="posts_list" disabled="disabled">Posts</option>').change();
		}
	}	
});


$(document).on("click", ".select2-selection__choice__remove", function(){
  var data = $('#export_pages').val();

  if (data == null) {
  	$('.select_multi_pages').show();
  }
});

$(document).on("click", ".select_multi_pages", function(){
	$('.select2-selection__rendered').click();
});


$(document).on("click", ".static_html_settings .nav-item .nav-link", function(e){

	e.preventDefault();

	$('.static_html_settings .nav-item .nav-link').removeClass('active');
	$('.static_html_settings .tab-pane').removeClass('active');
	$(this).addClass('active');

	var link = $(this).attr('href');
	$(link).addClass('active');

	var data= $(this).data('id');
	var url=window.location.href.split('#')[0];
	var to_url=url+"#"+data;
	window.location.href=to_url;

});

$(document).on("mouseenter", ".newly-added-list", function(){
	$(this).addClass('select2-results__option--highlighted');
});

$(document).on("click", ".newly-added-list", function(){
	var page_id = $(this).attr('value');

	 $('#export_pages').val(page_id).change();
});

function rc_ajax_select2(){
	rc_select2_for_posts();
}

function rc_select2_for_posts(post_status="", fieldTitle="Select a post") {
	$('#export_pages').select2({
		ajax: {
			url: rcewpp.ajax_url,
			dataType: 'json',
			delay: 250,
			data: function(params) {
				return {
					action: 'rcewpp_get_wp_posts',
					rc_nonce: rcewpp.nonce,
					q: params.term, // search term
					page: params.page || 1,
					post_status: post_status
				};
			},
			processResults: function(data, params) {
				params.page = params.page || 1;

				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			},
			cache: true
		},
		placeholder: fieldTitle,
		minimumInputLength: 0,
	});
}

$(document).on("click", ".select_posts_section .radio-container", function(e){
	setTimeout(function(){
		var ps = post_status = $('.select_posts_section [name="post_status"]:checked').val();
		if (post_status == "future"){
			ps = "scheduled";
		}
		rc_select2_for_posts(post_status, "Select a "+ps+" post");

		if (post_status != "publish"){
			$('#login_as').val('editor').change();
		}

	}, 100);



});


$(document).on("input", "#image_quality, #custom_image_quality", function(e){
	$(this).parent().siblings('input').val($(this).val())
});

$(document).on("keyup", "#image_quality_input, #custom_image_quality_input", function(e){
	$(this).siblings('.brightness-box').children('input').val($(this).val())
});




$(document).on("change", ".checkbox-container input", function(){
	if ($(this).is(':checked')) {
		$(this).parent().siblings('.export_html_sub_settings').slideDown();
	} else {
		$(this).parent().siblings('.export_html_sub_settings').slideUp();
	}
});

$(document).on("click", "#full_site", function(){
	if ($(this).is(':checked')) {
		$('#export_pages').val('home_page').trigger('change');
	}
});
$(document).on("click", "#selectAllPages", function(){
	if($(this).is(':checked') ){ //select all
		$("#export_pages").find('option').prop("selected",true);
		$("#export_pages").trigger('change');
	} else { //deselect all
		$("#export_pages").find('option').prop("selected",false);
		$("#export_pages").trigger('change');
	}
});

$(document).on("change", "#upload_to_ftp2", function(){
	if ($(this).is(':checked')) {
		$('.ftp_Settings_section2').slideDown();
	} else {
		$('.ftp_Settings_section2').slideUp();
	}
});

/*$(document).on("change", "#email_notification", function(){
	if ($(this).is(":checked")) {
		$('.email_settings_item').slideDown();
	} else {
		$('.email_settings_item').slideUp();
	}
});
$(document).on("change", "#email_notification2", function(){
	if ($(this).is(":checked")) {
		$('.email_settings_item2').slideDown();
	} else {
		$('.email_settings_item2').slideUp();
	}
});*/
function removeHtmlZipFile() {
  var txt;
  var r = confirm("Are you sure you would like to remove the file?");
  if (r == true) {
    return true;
  } else {
    return false;
  }
}

$(document).on("click", ".delete_zip_file", function(){
	var this_ = $(this);
	var file_name = this_.attr('file_name');
	if (removeHtmlZipFile()) {
		var datas = {
			'action': 'delete_exported_zip_file',
			'rc_nonce': rcewpp.nonce,
			'file_name': file_name,
		};


		$.ajax({
			url: rcewpp.ajax_url,
			data: datas,
			type: 'post',
			dataType: 'json',

			beforeSend: function(){

			},
			success: function(r){
				if(r.success == 'true'){

					this_.closest('.exported_zip_file').remove();


				} else {
					console.log('Something went wrong, please try again!');
				}

			}, error: function(){

			}
		});
	}
});

 $(document).on("click", ".support.my-2", function(e){
    e.preventDefault();

	 StopInterval();
	 console.log('Interval Stopped')
  });

 /*Save advanced settings*/
$(document).on("click", ".btn_save_settings", function(e){
    e.preventDefault();

    var createIndexOnSinglePage = $('#createIndexOnSinglePage').is(':checked') ? true : false;
    var saveAllAssetsToSpecificDir = $('#saveAllAssetsToSpecificDir').is(':checked') ? true : false;
    var keepSameName = $('[name="keepSameName"]:checked').val();
    var excludeUrls = $('#excludeUrls').val();
    var addContentsToTheHeader = $('#addContentsToTheHeader').val();
    var addContentsToTheFooter = $('#addContentsToTheFooter').val();

    var searchFor = $('#searchFor').val();
    var replaceWith = $('#replaceWith').val();

	var userRoles = $('[name*="user_roles"]:checked');
    var userRolesArray = [];
	$(userRoles).each(function (i, v) {
		userRolesArray.push($(v).val());
	})
     var datas = {
       'action': 'saveAdvancedSettings',
       'rc_nonce': rcewpp.nonce,
       'createIndexOnSinglePage': createIndexOnSinglePage,
       'saveAllAssetsToSpecificDir': saveAllAssetsToSpecificDir,
       'keepSameName': keepSameName,
       'excludeUrls': excludeUrls,
       'addContentsToTheHeader': addContentsToTheHeader,
       'addContentsToTheFooter': addContentsToTheFooter,

       'searchFor': searchFor,
       'replaceWith': replaceWith,

       'userRolesArray': userRolesArray,
     };

     $.ajax({
         url: rcewpp.ajax_url,
         data: datas,
         type: 'post',
         dataType: 'json',

         beforeSend: function(){
			$('.btn_save_settings .spinner_x').removeClass('hide_spin');
         },
         success: function(r){
            if(r.success){
                $('.badge_save_settings').show();
				$('.btn_save_settings .spinner_x').addClass('hide_spin');

                setTimeout(function(){
					$('.badge_save_settings').hide();
				}, 5000)
            } else {
                console.log('Something went wrong, please try again!');
				$('.btn_save_settings .spinner_x').addClass('hide_spin');
            }
         },
         error: function(){
            console.log('Something went wrong, please try again!');
			 $('.btn_save_settings .spinner_x').addClass('hide_spin');
         }
     });
});


$(document).on("click", "#test_ftp_connection", function(e){
	e.preventDefault();

	var ftp_data = {};
	if ($('#ftp_host2').val() !== "") {
		ftp_data['host'] = $('#ftp_host3').val();
	}

	if ($('#ftp_user2').val() !== "") {
		ftp_data['user'] = $('#ftp_user3').val();
	}

	if ($('#ftp_pass2').val() !== "") {
		ftp_data['pass'] = $('#ftp_pass3').val();
	}

	if ($('#ftp_path2').val() !== "") {
		ftp_data['path'] = $('#ftp_path3').val();
	}

	var ftp_data = JSON.stringify(ftp_data)
	
	 var datas = {
	  'action': 'rc_check_ftp_connection_status',
	  'rc_nonce': rcewpp.nonce,
	  'ftp_data': ftp_data,
	};
	
	$.ajax({
	    url: rcewpp.ajax_url,
	    data: datas,
	    type: 'post',
	    dataType: 'json',
	
	    beforeSend: function(){
	
	    },
	    success: function(r){
	      	if(r.success == 'true'){
	        	console.log(r.response);
				if (r.response) {
					$('.tab_ftp_status').addClass('connected').removeClass('not_connected');
					$('.ftp_status .ftp_connected').show();
					$('.ftp_status .ftp_not_connected').hide();
					$('.ftp_authentication_failed').hide();
					$('.ftp_upload_checkbox').removeClass('ftp_disabled');
					$('.ftp_upload_checkbox').find('input').attr('disabled', 'disabled');
					setTimeout(function() {
						window.location.reload();
					}, 3000);
				}
				else{
					$('.tab_ftp_status').removeClass('connected').addClass('not_connected');
					$('.ftp_status .ftp_connected').hide();
					$('.ftp_status .ftp_not_connected').text('Authentication failed').show();
					$('.ftp_authentication_failed').show();
					$('.ftp_upload_checkbox').addClass('ftp_disabled');
					$('.ftp_upload_checkbox').find('input').removeAttr('disabled');
				}
	        
	        } else {
	          console.log('Something went wrong, please try again!');
	        }
	    	
	    }, error: function(){
	    	
	  }
	});
});

$(document).on("click", ".ftp_dark_blur", function(){
	$(this).fadeOut(300);
	$('.ftp_path_select').fadeOut(300);
});

$(document).on("click", ".ftp_path_browse1", function(e){
	e.preventDefault();
	$(".ftp_dark_blur").fadeIn(300);
	$('.ftp_path_select').fadeIn(300);

	 var datas = {
	  'action': 'get_ftp_dir_file_list',
	  'rc_nonce': rcewpp.nonce,
	  'path': $('#ftp_path').val(),
	};
	
	$.ajax({
	    url: rcewpp.ajax_url,
	    data: datas,
	    type: 'post',
	    dataType: 'json',
	
	    beforeSend: function(){
			$('.loading_section').show();
			$('.ftp_path_select .spinner_x').removeClass('hide_spin');
			$('.ftp_path_select .list-group').addClass('blurry');

	    },
	    success: function(r){
			console.log(r);

	      if(r.success == 'true'){
	       // console.log(r.response);
			$('.ftp_dir_lists').html(r.response);
	        
	        } else {
	          console.log('Something went wrong, please try again!');
	        }

			$('.loading_section').hide();
			$('.ftp_path_select .list-group').removeClass('blurry');
	    	
	    }, error: function(){
	    	
	  }
	});
});

$(document).on("click", ".ftp_path_select .list-group-item", function(){
	var path = $(this).attr('dir_path');
	 var datas = {
	  'action': 'rc_html_export_get_dir_path',
	  'rc_nonce': rcewpp.nonce,
	  'path': path,
	};
	
	$.ajax({
	    url: rcewpp.ajax_url,
	    data: datas,
	    type: 'post',
	    dataType: 'json',
	
	    beforeSend: function(){
			$('.loading_section').show();
			$('.ftp_path_select .spinner_x').removeClass('hide_spin');
			$('.ftp_path_select .list-group').addClass('blurry');
	    },
	    success: function(r){
	      if(r.success == 'true'){
	        //console.log(r.response);
	
	        $('.ftp_dir_lists').html(r.response);
	        } else {
	          console.log('Something went wrong, please try again!');
	        }
			$('.loading_section').hide();
			$('.ftp_path_select .list-group').removeClass('blurry');
	    	
	    }, error: function(){
	    	
	  }
	});
});

$(document).on("click", ".ftp_select_path", function(e){
	e.preventDefault();
	var current_path = $('.ftp_current_path').text();
	$('#ftp_path, #ftp_path2').val(current_path);
	$('.ftp_dark_blur').click();
});

$(document).on("click", ".ftp_disabled", function(){
	alert("Please connect to the ftp server first from the \"FTP Settings\" tab");
});

$(document).on("click", ".cancel_rc_html_export_process", function(e){
	e.preventDefault();
	stop_export_log_percentage();
	$('.export_internal_page_to_html, .export_external_page_to_html').removeAttr('disabled').siblings('.spinner_x').addClass('hide_spin').siblings('.cancel_rc_html_export_process').hide();
	
	$('#cancel_ftp_process').val('true');

	var datas = {
	  'action': 'cancel_rc_html_export_process',
	  'rc_nonce': rcewpp.nonce,
	  'post2': '',
	};
	
	$.ajax({
	    url: rcewpp.ajax_url,
	    data: datas,
	    type: 'post',
	    dataType: 'json',
	
	    beforeSend: function(){
	
	    },
	    success: function(r){
	      	if(r.success == 'true'){
				if(!$('.log.cancel_command').length){
					$('.logs_list').prepend('<div class="log main_log cancel_command" id="48"><span class="danger log_type">Export process has been canceled!</span></div>')
				}
	        } else {
	          console.log('Something went wrong, please try again!');
	        }
	    	
	    }, error: function(){
	    	
	  	}
	});
});

$(document).on("click", "#check_all_files", function () {
	if ($(this).is(':checked')){
		$('.exported_zip_file input').prop('checked', true)
	}
	else{
		$('.exported_zip_file input').prop('checked', false)
	}
});

$(document).on("click", ".submit_files_action", function (e) {
	e.preventDefault();
	var action = $('#files_action').val();
	var fileIds = [];

	if (action.length == 0){
		alert('Please select an action.');
		return false;
	}
	if ($('.exported_zip_file input:checked').length > 0){
		$('.exported_zip_file input:checked').each(function(){
			fileIds.push($(this).val())
		})
	}
	else{
		alert('Please check atleast 1 file.');
		return false;
	}

	var datas = {
		'action': 'rc_html_export_files_action',
		'rc_nonce': rcewpp.nonce,
		'files_action': action,
		'fileIds': fileIds,
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

				$('.exported_zip_file input:checked').each(function(){
					if (action=='remove'){
						$(this).parent().remove()
					}
					else if(action=='hide'){
						$(this).parent().addClass('hidden').removeClass('visible');
					}
					else if(action=='visible'){
						$(this).parent().removeClass('visible').removeClass('hidden');
					}
				})


			} else {
				console.log('Something went wrong, please try again!');
			}

		}, error: function () {

		}
	});
});

$(document).on("click", ".show_hidden_files", function (e) {
	e.preventDefault();

	$('.exported_zip_file').each(function(){
		if ($(this).hasClass('hidden')){
			$(this).addClass('visible').removeClass('hidden');
		}
	})
});


$(document).on("click", ".select_all_posts", function (e) {
	//$('#search_posts_to_select2').prop('checked', false);
});
$(document).on("click", ".select_all_posts", function (e) {
	//$('#search_posts_to_select2').prop('checked', false);
});

$(document).ready(function () {
	setTimeout(function(){
		if (window.location.hash) {
			var hash = window.location.hash.substring(1);
			$('[data-id="'+hash+'"]').click();
		}
	}, 500);


});

jQuery(document).ready(function($) {
	// Dismiss the notice
	$('.wp-plugin-dismiss').click(function(e) {
		e.preventDefault();
		$.post(rcewpp.ajax_url, {
			action: 'wp_plugin_dismiss_notice',
			nonce: rcewpp.nonce,
		}, function(response) {
			if (response.success) {
				$('.wp-plugin-notice').hide();
			}
		});
	});

	// Close the notice forever
	$('.wp-plugin-close').click(function(e) {
		e.preventDefault();
		$.post(rcewpp.ajax_url, {
			action: 'wp_plugin_close_notice',
			nonce: rcewpp.close_nonce,
		}, function(response) {
			if (response.success) {
				$('.wp-plugin-notice').hide();
			}
		});
	});
});



function createRandomId(length) {
	let result = '';
	const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	const charactersLength = characters.length;
	let counter = 0;
	while (counter < length) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
		counter += 1;
	}
	return result;
}