<div class="uploadingFilesToFtpLogs" style="display: none;">
    <h4 class="progress-title p-t-15"><?php _e('Uploading Files to Ftp', 'export-wp-page-to-static-html'); ?></h4>

    <span class="totalUploadedFilesToFtp" style="margin-right: 10px"><?php _e('Uploaded:', 'export-wp-page-to-static-html'); ?> <span class="total_uploaded_files_to_ftp progress_">0</span></span>
    <span class="totalFilesToUpload"><?php _e('Total files:', 'export-wp-page-to-static-html'); ?> <span class="total_files_to_upload total_">0</span></span>

    <div class="progress green" style="margin-top: 20px">
        <div class="progress-bar" style="width:90%; background:#4daf7c;">
            <div class="progress-value">0%</div>
        </div>
    </div>
    <div class="export_failed error" style="display: none;"><?php _e('Upload failed! Check your network connection!', 'export-wp-page-to-static-html'); ?></div>
</div>