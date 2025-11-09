<div class="tab-pane custom_links" id="tabs-2" role="tabpanel" style="position: relative;">

    <div class="pro-mask">
        <div class="pro-content">
            <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=custom_url" class="go-pro-btn">🚀 Go to Pro</a>
            <p class="pro-note">Unlock advanced features with Pro</p>
        </div>
    </div>

    <!-- Your original locked content -->
    <div class="locked-content">
        <div class="custom_link_section">
           <input type="text" id="custom_link" name="custom_link" placeholder="https://example.com" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
        </div>
        <p style="font-size:13px; color:#555; margin-top:6px;"><?php _e('Enter the any website URL that you want to export as static HTML/CSS.', 'export-wp-page-to-static-html'); ?></p>



    <div class="p-t-10">
        <label class="checkbox-container m-r-45"><?php _e('Full site (must use homepage url)', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="full_site2" name="full_site">
            <span class="checkmark"></span>
        </label>
    </div>

    <div class="p-t-10">
        <label class="checkbox-container m-r-45"><?php _e('Replace all url to #', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="replace_all_url2" name="replace_all_url">
            <span class="checkmark"></span>
        </label>
    </div>


    <div class="p-t-10">
        <label class="checkbox-container m-r-45" for="custom_url_skip_assets"><?php _e('Skip Assets (Css, Js, Images or Videos)', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="custom_url_skip_assets" name="custom_url_skip_assets">
            <span class="checkmark"></span>
        </label>

        <div class="skip_assets_subsection export_html_sub_settings">
            <label class="checkbox-container m-r-45" for="custom_url_skip_stylesheets"><?php _e('Skip Stylesheets (.css)', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_stylesheets" name="custom_url_skip_stylesheets" checked>
                <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="custom_url_skip_scripts"><?php _e('Skip Scripts (.js)', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_scripts" name="custom_url_skip_scripts" checked>
                <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="custom_url_skip_images"><?php _e('Skip Images', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_images" name="custom_url_skip_images" checked>
                <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="custom_url_skip_videos"><?php _e('Skip Videos', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_videos" name="custom_url_skip_videos" checked>
                <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="custom_url_skip_audios"><?php _e('Skip Audios', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_audios" name="custom_url_skip_audios" checked>
                <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="custom_url_skip_docs"><?php _e('Skip Documents', 'export-wp-page-to-static-html'); ?>
                <input type="checkbox" id="custom_url_skip_docs" name="custom_url_skip_docs" checked>
                <span class="checkmark"></span>
            </label>
        </div>

    </div>


    <div class="p-t-10">
        <label class="checkbox-container m-r-45" for="custom_image_to_webp"><?php _e('Compress images size (image to webp)', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="custom_image_to_webp" name="custom_image_to_webp">
            <span class="checkmark"></span>
        </label>


        <div class="image_to_webp_subsection export_html_sub_settings">
            <div class="brightness-box">
                <input type="range" id="custom_image_quality" min="10" max="100" value="80">
            </div>
            <input type="text" id="custom_image_quality_input" value="80" style="width: 45px;" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
        </div>

    </div>

    <div class="p-t-10">
        <label class="checkbox-container ftp_upload_checkbox m-r-45 <?php
        if ($ftp_status !== 'connected') {
            echo 'ftp_disabled';
        }
        ?>"><?php _e('Upload to ftp', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="upload_to_ftp2" name="upload_to_ftp"

                <?php
                if ($ftp_status !== 'connected') {
                    echo 'disabled=""';
                }
                ?>
            >
            <span class="checkmark"></span>
        </label>

        <div class="ftp_Settings_section2 export_html_sub_settings">


            <!--  <div class="ftp_settings_item">
                                                <input type="text" id="ftp_host2" name="ftp_host" placeholder="Host" value="<?php echo $host; ?>">
                                            </div>
                                            <div class="ftp_settings_item">
                                                <input type="text" id="ftp_user2" name="ftp_user" placeholder="User" value="<?php echo $user; ?>">
                                            </div>
                                            <div class="ftp_settings_item">
                                                <input type="password" id="ftp_pass2" name="ftp_pass" placeholder="Password" value="<?php echo $pass; ?>">
                                            </div> -->
            <div class="ftp_settings_item">
                <label for="ftp_path2"><?php _e('FTP upload path', 'export-wp-page-to-static-html'); ?></label>
                <input type="text" id="ftp_path2" name="ftp_path" placeholder="Upload path" value="<?php echo $path; ?>">
                <div class="ftp_path_browse1"><a href="#"><?php _e('Browse', 'export-wp-page-to-static-html'); ?></a></div>
            </div>
        </div>
    </div>


    <!-- <div class="p-t-10">
        <label class="checkbox-container run_task_in_bg m-r-45"><?php _e('Run in background', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="run_task_in_bg2" name="run_task_in_bg2" >
            <span class="checkmark"></span>
        </label>

        <div class="email_settings_section export_html_sub_settings">
            <div class="email_settings_item2">
                <label for="run_task_in_bg2" class="checkbox-container m-r-45"><?php _e('Receive notification when complete', 'export-wp-page-to-static-html'); ?>
                    <input type="checkbox" id="email_notification2" name="email_notification">
                    <span class="checkmark"></span>
                </label>

                <div class="email_settings_item2 export_html_sub_settings">
                    <input type="text" id="receive_notification_email2" name="notification_email" placeholder="Enter emails (optional)">
                    <span><?php _e('Enter emails seperated by comma (,) (optional)', 'export-wp-page-to-static-html'); ?></span>
                </div>
            </div>


        </div>
    </div>


    <div class="p-t-10">
        <div class="custom_util_settings_section">
            <div class="custom_util_settings_item">
                <label class="checkbox-container m-r-45"><?php _e('Alternative Export (if any issues appear previously)', 'export-wp-page-to-static-html'); ?>
                    <input type="checkbox" id="custom_alt_export" name="custom_alt_export">
                    <span class="checkmark"></span>
                </label>
            </div>
        </div>
    </div> -->

    <div class="p-t-20"></div>

    <button class="flat-button primary export_external_page_to_html" type="submit"><?php _e('Export HTML', 'export-wp-page-to-static-html'); ?> </button>
    <span class="spinner_x hide_spin"></span>
    <a class="cancel_rc_html_export_process" href="#">
        <?php _e('Cancel', 'export-wp-page-to-static-html'); ?>
    </a>

    <a href="" class="action-btn download-btn hide">
        <span class="icon">
            <!-- Download Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
        </span>
        <?php _e('Download the Zip File', 'export-wp-page-to-static-html'); ?>
        </a>
    <a href="" class="view_exported_file hide" target="_blank">
        <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
        <?php _e('View Exported File', 'export-wp-page-to-static-html'); ?>
    </a>

    
    </div>
</div>