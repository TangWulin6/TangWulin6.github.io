
<div class="col-3 p-10 dev_section" >

    <div class="created_by py-2 mt-1 border-bottom"> <?php _e('Created by', 'export-wp-page-to-static-html'); ?> <a href="https://myrecorp.com"><img src="<?php echo EWPPTSH_PLUGIN_DIR_URL . '/admin/images/recorp-logo.png'; ?>" alt="ReCorp" width="100"></a></div>


    <div class="documentation my-2">
        <a href="https://myrecorp.com/documentation/export-wp-page-to-html"><span><?php _e('Documentation', 'export-wp-page-to-static-html'); ?> </span></a>
    </div>

    <div class="documentation my-2">
        <a href="https://myrecorp.com/support"><span><?php _e('Support', 'export-wp-page-to-static-html'); ?> </span></a>
    </div>

    <div class="pro-content">
        <a href="https://myrecorp.com/export-wp-pages-to-static-html-css-pro?ref=custom_url" class="go-pro-btn">🚀 Go to Pro</a>
        <p class="pro-note">Unlock advanced features with Pro</p>
    </div>
    
    <?php
    if (!isset($_GET['welcome'])&&!(PHP_VERSION_ID >= 70205)) {
        echo $versionIssue;
    }
    ?>


    <div class="right_side_notice mt-4">
        <?php echo do_action('wpptsh_right_side_notice'); ?>
    </div>
</div>