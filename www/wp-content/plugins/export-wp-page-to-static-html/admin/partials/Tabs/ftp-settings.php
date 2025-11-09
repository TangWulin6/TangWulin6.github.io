<div class="tab-pane" id="tabs-4" role="tabpanel" style="position: relative">
    
    <div class="pro-mask">
        <div class="pro-content">
            <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=ftp-settings" class="go-pro-btn">🚀 Go to Pro</a>
            <p class="pro-note">Unlock advanced features with Pro</p>
        </div>
    </div>

    <div class="locked-content">
        
        <div class="ftp_Settings_section3">

            <div class="ftp_settings_item">
                <label for="ftp_host3"><?php _e('FTP host', 'export-wp-page-to-static-html'); ?></label>
                <input type="text" id="ftp_host3" name="ftp_host" placeholder="Host" value="<?php echo $host; ?>">
            </div>
            <div class="ftp_settings_item">
                <label for="ftp_user3"><?php _e('FTP user', 'export-wp-page-to-static-html'); ?></label>
                <input type="text" id="ftp_user3" name="ftp_user" placeholder="User" value="<?php echo $user; ?>">
            </div>
            <div class="ftp_settings_item">
                <label for="ftp_pass3"><?php _e('FTP password', 'export-wp-page-to-static-html'); ?></label>
                <input type="password" id="ftp_pass3" name="ftp_pass" placeholder="Password" value="<?php echo $pass; ?>">
            </div>
            <div class="ftp_settings_item">
                <label for="ftp_path3"><?php _e('FTP upload path (deafult)', 'export-wp-page-to-static-html'); ?></label>
                <input type="text" id="ftp_path3" name="ftp_path" placeholder="Upload path" value="<?php echo $path; ?>">
            </div>


            <div class="ftp_status_section"><span class="ftp_status_text"><?php _e('FTP connection status: ', 'export-wp-page-to-static-html'); ?></span><span class="ftp_status">
                                                <?php
                                                if ( $ftp_status == 'connected' ): ?>
                                                    <span class="ftp_connected">Connected</span>
                                                    <span class="ftp_not_connected" style="display: none;"><?php _e('Not Connected', 'export-wp-page-to-static-html'); ?></span>

                                                <?php else: ?>
                                                    <span class="ftp_connected" style="display: none;"><?php _e('Connected', 'export-wp-page-to-static-html'); ?></span>
                                                    <span class="ftp_not_connected"><?php _e('Not Connected', 'export-wp-page-to-static-html'); ?></span>
                                                <?php endif ?>
                                            </span>

            </div>
            <div class="ftp_authentication_failed" style="<?php if ( $ftp_status == 'connected' ): ?>
                display: none;
            <?php endif ?>">
                <?php _e('<span style="font-weight: bold;">Error: </span>Host name or username or password is wrong. Please check and try again!', 'export-wp-page-to-static-html'); ?>
            </div>

            <button id="test_ftp_connection" class="btn btn--radius-2 btn--green" style="margin-top: 15px;"><?php _e('Test Connection', 'export-wp-page-to-static-html'); ?></button>
        </div>
    </div>
</div>