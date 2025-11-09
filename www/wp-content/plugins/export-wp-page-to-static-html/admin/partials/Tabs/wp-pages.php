<div class="tab-pane active" id="tabs-1" role="tabpanel">

    <form method="POST" class="pt-3">
        <div class="input-group select-a-page-input-group">
            <label class="label" for="export_pages" style="font-weight:600; display:block; margin-bottom:6px; font-size:14px;">
                <?php _e('Select Pages to Export', 'export-wp-page-to-static-html'); ?>
            </label>


            <div class="rs-select2 js-select-simple select--no-search">
                <select id="export_pages" name="export_pages" multiple>
                    <option value="home_page" permalink="<?php echo home_url('/'); ?>" filename="homepage">
                        <?php _e('Homepage', 'export-wp-page-to-static-html');?> (<?php echo home_url(); ?>)
                    </option>

                    <?php
                    if (!empty($query->posts)) {
                        foreach ($query->posts as $key => $post) {
                            $post_id = $post->ID;
                            $post_title = $post->post_title;
                            $permalink = get_the_permalink($post_id);
                            $parts = parse_url($permalink);

                            if(isset($parts['query'])){
                                parse_str($parts['query'], $query);
                            }
                            else{
                                $query = "";
                            }

                            if (!empty($query)) {
                                $permalink = strtolower(str_replace(" ", "-", $post_title));
                            }

                            $private = '';
                            if ($post->post_status == "private"){
                                $private = __(' (private)', 'export-wp-page-to-static-html');
                            }

                            if(!empty($post_title)){
                                if ($post->post_status == "private"){
                                    ?>
                                    <option value="<?php echo $post_id; ?>" permalink="<?php echo basename($permalink); ?>">
                                        <?php echo $post_title . $private; ?>
                                    </option>
                                    <?php
                                }
                                else{
                                    ?>
                                    <option value="<?php echo $post_id; ?>" permalink="<?php echo basename($permalink); ?>">
                                        <?php echo $post_title; ?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </select>
                <div class="select-dropdown"></div>
            </div>
            
            <p style="font-size:13px; color:#555; margin:4px 0 10px;">
                Choose one or more pages from your site to export as static HTML/CSS.
            </p>
            
            <div class="checkbox-fullsite">
                <div class="checkbox-lock">
                    <label class="checkbox-container full_site m-r-45">Full Site
                        <input type="checkbox" id="full_site" name="full_site" disabled><span class="badge-pro">Pro</span>
                        <span class="checkmark"></span>
                    </label>
                    <p style="font-size:13px; color:#555; margin:4px 0 10px;">
                        Export everything by crawling internal links. (Available in Pro)
                    </p>

                    <div class="go-pro-popup">
                        <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=full_site_btn" class="go-pro-btn">🚀 Go to Pro</a>
                        <p class="pro-note">Unlock advanced features with Pro</p>
                    </div>
                </div>
            </div>

            
            <div class="seach_posts">
                <div class="checkbox-lock">
                <div class="">
                    <label class="checkbox-container m-r-45 disabled"><?php _e('Select posts', 'export-wp-page-to-static-html'); ?>
                        <input type="checkbox" id="search_posts_to_select2" name="search_posts_to_select2" disabled><span class="badge-pro">Pro</span>
                        <span class="checkmark"></span>
                    </label>
                    <p style="font-size:13px; color:#555; margin:4px 0 10px;">
                        Include blog posts in the export. (Available in Pro)
                    </p>


                    <div class="select_posts_section export_html_sub_settings">
                        <div class="p-t-10">

                            <label class="radio-container m-r-45"><?php _e('Published posts only', 'export-wp-page-to-static-html'); ?>
                                <input type="radio" id="published_posts_only" name="post_status" value="publish">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-container m-r-45"><?php _e('Draft posts only', 'export-wp-page-to-static-html'); ?>
                                <input type="radio" id="draft_posts_only" name="post_status" value="draft">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-container m-r-45"><?php _e('Private posts only', 'export-wp-page-to-static-html'); ?>
                                <input type="radio" id="private_posts_only" name="post_status" value="private">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-container m-r-45"><?php _e('Pending posts only', 'export-wp-page-to-static-html'); ?>
                                <input type="radio" id="pending_posts_only" name="post_status" value="pending">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-container m-r-45"><?php _e('Scheduled posts only', 'export-wp-page-to-static-html'); ?>
                                <input type="radio" id="future_posts_only" name="post_status" value="future">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>

                    <div class="go-pro-popup">
                        <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=select_posts_btn" class="go-pro-btn">🚀 Go to Pro</a>
                        <p class="pro-note">Unlock advanced features with Pro</p>
                    </div>
                </div>


            </div>

            

            

            <!-- <div class="select_pages_to_export">
                <ul class="pages_list">
                </ul>
            </div> -->
        </div>

<!-- Advanced settings collapsible (HTML + CSS only) -->
<style>
/* Collapsible "Advanced settings" styles (pure CSS) */
.adv-settings{border:1px solid #e5e7eb; border-radius:10px; background:#fff;}
.adv-settings[open]{box-shadow:0 6px 20px rgba(0,0,0,.06)}
.adv-summary{list-style:none; cursor:pointer; padding:12px 14px; display:flex; align-items:center; gap:10px; font-weight:700}
.adv-summary::marker{display:none}
.adv-summary .caret{width:10px; height:10px; border-right:2px solid #9aa3b2; border-bottom:2px solid #9aa3b2; transform:rotate(-45deg); transition:transform .25s ease}
.adv-settings[open] .adv-summary .caret{transform:rotate(45deg)}
.adv-summary .meta{margin-left:auto; color:#64748b; font-weight:600; font-size:12px}
.adv-panel{max-height:0; overflow:hidden; transition:max-height .35s ease; border-top:1px solid #eef1f5}
.adv-settings[open] .adv-panel{max-height:2000px; padding: 10px 25px 25px 25px;}
/* Optional: light admin-friendly look; remove if you have your own styles */
.checkbox-container{display:inline-flex; align-items:center; gap:8px}
.p-t-10{padding-top:10px}
.m-r-45{margin-right:45px}
.input-group{display:flex; flex-direction:column; gap:6px}
.label{font-weight:600}
.brightness-box{display:inline-block; margin-right:8px}
.checkbox-lock{position:relative}
.go-pro-popup{margin-top:8px}
.badge-pro {
	margin-left: 8px;
	font-size: 11px;
	font-weight: 700;
	line-height: 1;
	padding: 4px 6px;
	border-radius: 6px;
	background: linear-gradient(135deg, #ff6a00, #ee0979);
	color: #fff;
	border: 1px solid rgba(0,0,0,.08);
}
</style>

<details class="adv-settings" id="advanced_settings" aria-label="Advanced settings">
  <summary class="adv-summary">
    <span class="caret" aria-hidden="true"></span>
    Advanced settings
    <span class="meta">Optional</span>
  </summary>
  <div class="adv-panel">
    <div class="col-8">
      <div class="p-t-10">
        <div class="input-group">
          <label class="label label_login_as" style="font-weight: bold" for="login_as"><?php _e('Login as (optional)', 'export-wp-page-to-static-html'); ?></label>
          <select id="login_as" name="login_as">
            <option value="" selected=""><?php _e('Select a user role', 'export-wp-page-to-static-html'); ?></option>
            <?php
            global $wp_roles;
            $all_roles = $wp_roles->roles;
            if(!empty($all_roles)){
                foreach($all_roles as $key => $role){
                    echo '<option value="'.$key.'">' . $role['name'] . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <div class="p-t-10">
          <label class="checkbox-container m-r-45"><?php _e('Replace all url to #', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="replace_all_url" name="replace_all_url">
            <span class="checkmark"></span>
          </label>
        </div>

        <div class="p-t-10">
          <label class="checkbox-container m-r-45" for="skip_assets"><?php _e('Skip Assets (Css, Js, Images or Videos)', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="skip_assets" name="skip_assets">
            <span class="checkmark"></span>
          </label>

          <div class="skip_assets_subsection export_html_sub_settings">
            <label class="checkbox-container m-r-45" for="skip_stylesheets"><?php _e('Skip Stylesheets (.css)', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_stylesheets" name="skip_stylesheets" checked>
              <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="skip_scripts"><?php _e('Skip Scripts (.js)', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_scripts" name="skip_scripts" checked>
              <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="skip_images"><?php _e('Skip Images', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_images" name="skip_images" checked>
              <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="skip_videos"><?php _e('Skip Videos', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_videos" name="skip_videos" checked>
              <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="skip_audios"><?php _e('Skip Audios', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_audios" name="skip_audios" checked>
              <span class="checkmark"></span>
            </label>

            <label class="checkbox-container m-r-45" for="skip_docs"><?php _e('Skip Documnets', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="skip_docs" name="skip_docs" checked>
              <span class="checkmark"></span>
            </label>
          </div>
        </div>

        <div class="p-t-10">
          <label class="checkbox-container m-r-45" for="image_to_webp"><?php _e('Compress images size (image to webp)', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="image_to_webp" name="image_to_webp">
            <span class="checkmark"></span>
          </label>

          <div class="image_to_webp_subsection export_html_sub_settings">
            <div class="brightness-box">
              <input type="range" id="image_quality" min="10" max="100" value="80">
            </div>
            <input type="text" id="image_quality_input" value="80" style="width:45px;" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
          </div>
        </div>

        <div class="p-t-10">
          <div class="checkbox-lock" style="margin-top:0;">
            <label class="checkbox-container ftp_upload_checkbox m-r-45 <?php if ($ftp_status !== 'connected') { echo 'disabled'; } ?>"><?php _e('Upload to ftp', 'export-wp-page-to-static-html'); ?>
              <input type="checkbox" id="upload_to_ftp" name="upload_to_ftp" <?php if ($ftp_status !== 'connected') { echo 'disabled=""'; } ?> >
              <span class="checkmark"></span>
            </label>

            <div class="ftp_Settings_section export_html_sub_settings">
              <div class="ftp_settings_item">
                <label for="ftp_path"><?php _e('FTP upload path', 'export-wp-page-to-static-html'); ?></label>
                <input type="text" id="ftp_path" name="ftp_path" placeholder="Upload path" value="<?php echo $path; ?>">
                <div class="ftp_path_browse1"><a href="#"><?php _e('Browse', 'export-wp-page-to-static-html'); ?></a></div>
              </div>
            </div>

            <div class="go-pro-popup">
              <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=ftp_select" class="go-pro-btn">🚀 Go to Pro</a>
              <p class="pro-note">Unlock advanced features with Pro</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</details>

        <div class="p-t-15">
            <button class="flat-button primary export_internal_page_to_html" type="submit"><?php _e('Export HTML', 'export-wp-page-to-static-html'); ?> </button>
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
                View Exported File
            </a>
            <div class="error-notice" style="display: none;">
                <p><?php _e('Something went wrong! please try again. If failed continously then contact us.', 'export-wp-page-to-static-html'); ?></p>
            </div>
        </div>
    </form>

</div>


