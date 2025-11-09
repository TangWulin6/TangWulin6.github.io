<div class="creatingZipFileLogs" style="display: none;">
    <h4 class="progress-title p-t-15"><?php _e('Creating Zip File', 'export-wp-page-to-static-html'); ?></h4>

    <span class="totalPushedFilesToZip" style="margin-right: 10px"><?php _e('Created:', 'export-wp-page-to-static-html'); ?> <span class="total_pushed_files_to_zip progress_">0</span></span>
    <span class="totalFilesToPush"><?php _e('Total files:', 'export-wp-page-to-static-html'); ?> <span class="total_files_to_push total_">0</span></span>

    <div class="progress blue" style="margin-top: 20px">
        <div class="progress-bar" style="width:90%; background:#1a4966;">
            <div class="progress-value">0%</div>
        </div>
    </div>
    <div class="export_failed error" style="display: none;"><?php _e('Error, failed to create zip file!', 'export-wp-page-to-static-html'); ?> </div>
</div>