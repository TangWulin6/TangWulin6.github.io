<?php

    /**
     * Provide a admin area view for the plugin
     *
     * This file is used to markup the admin-facing aspects of the plugin.
     *
     * @link       https://www.upwork.com/fl/rayhan1
     * @since      1.0.0
     *
     * @package    Export_Wp_Page_To_Static_Html
     * @subpackage Export_Wp_Page_To_Static_Html/admin/partials
     */


    $args = array(
        'post_type' => 'page',
        'post_status' => ['publish', 'private'],
        'posts_per_page' => '-1'
    );

    $query = new WP_Query( $args );

    $ftp_status = get_option('rc_export_html_ftp_connection_status');


    $ftp_data = get_option('rc_export_html_ftp_data');

    $host = isset($ftp_data->host) ? $ftp_data->host : "";
    $user = isset($ftp_data->user) ? $ftp_data->user : "";
    $pass = isset($ftp_data->pass) ? $ftp_data->pass : "";
    $path = isset($ftp_data->path) ? $ftp_data->path : "";

    $createIndexOnSinglePage = get_option('rcExportHtmlCreateIndexOnSinglePage', 'on');
    $saveAllAssetsToSpecificDir = get_option('rcExportHtmlSaveAllAssetsToSpecificDir', 'on');
    $keepSameName = get_option('rcExportHtmlKeepSameName', 'off');
    $addContentsToTheHeader = get_option('rcExportHtmlAddContentsToTheHeader');
    $addContentsToTheFooter = get_option('rcExportHtmlAddContentsToTheFooter');

    $searchFor = get_option('rcExportHtmlSearchFor');
    $replaceWith = get_option('rcExportHtmlReplaceWith');

    $excludeUrls = get_option('rcExportHtmlExcludeUrls', "%/wp-login.php\n%/wp-admin");


$versionIssue = sprintf('If the plugin does not work perfectly then it\'s require a PHP version ">= 7.2.5. You are running %s.', PHP_VERSION);
$versionIssue = __('<div class="danger" style="color: white;margin-bottom: 46px;background-color: #f21212d6;padding: 10px;">'.$versionIssue.'</div>', 'export-wp-page-to-static-html');

$upload_dir = wp_upload_dir()['basedir'] . '/exported_html_files/';
$upload_url = wp_upload_dir()['baseurl'] . '/exported_html_files/';

$d = dir($upload_dir);


function rcwpth_hidden_class($filename){
        $rcwph_files_hide = get_option('rcwph_hidden_files');
        if (!empty($rcwph_files_hide)){
            foreach ($rcwph_files_hide as $key => $item) {
                if ($item == $filename){
                    return "hidden";
                    break;
                }
            }
        }

        return "";
    }
?>

<div class="page-wrapper p-b-100 font-poppins static_html_settings">
    <div class="wrapper">
        <div class="card card-4">
            <div class="card-body">
                <h2 class="title"><?php _e('Export WP Pages to Static HTML/CSS', 'export-wp-page-to-static-html'); ?><span class="badge badge-success" style="position: relative;top: -4px;font-size: 15px;margin-left: 8px;">Free</span><span class="badge badge-dark version">v<?php echo EXPORT_WP_PAGE_TO_STATIC_HTML_VERSION; ?></span></h2>

                <!-- <div class="error-notice" style="background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;margin-bottom: 20px;">
                    <p><?php // echo __('Every site environment is unique, if your site failed to export to html then <a href="https://myrecorp.com/contact-us/" style="color: #721c24; font-weight: bold;">contact us.</a>. We\'ll try to help you as soon as possible.', 'export-wp-page-to-static-html'); ?></p>
                </div> -->

                <?php if (!extension_loaded('zip')) {
                    ?>
                    <div class="error-notice">
                        <p><?php _e('This plugin requires the Zip extension, which is not installed or enabled on your server. Without the Zip extension, the plugin will not function correctly. Please enable the Zip extension to export zip file of html/css.', 'export-wp-page-to-static-html'); ?></p>
                    </div>
                    <?php
                }?>

                <?php
                if (isset($_GET['welcome'])&&!(PHP_VERSION_ID >= 70205)) {
                    echo $versionIssue;
                }
                ;?>

                <div class="row">
                    <div class="col-7">
                        <div class=" export_html main_settings_page ">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-id="tab1" data-toggle="tab" href="#tabs-1" role="tab">WP Pages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-id="tab2" data-toggle="tab" href="#tabs-2" role="tab">Custom urls</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-id="tab3" data-toggle="tab" href="#tabs-3" role="tab">All Exported Files</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-6" role="tab">PDF Settings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-id="tab4" data-toggle="tab" href="#tabs-4" role="tab">FTP Settings <span class="tab_ftp_status <?php echo $ftp_status; ?>"></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-id="tab5" data-toggle="tab" href="#tabs-5" role="tab">Advanced Settings</a>
                                </li>
                            </ul><!-- Tab panes -->
                            <div class="tab-content">
                                <?php
                                    include 'Tabs/wp-pages.php';
                                    include 'Tabs/custom-url.php';
                                    include 'Tabs/all-zip-files.php';
                                    include 'Tabs/ftp-settings.php';
                                    include 'Tabs/pdf-settings.php';
                                    include 'Tabs/advanced-settings.php';
                                ?>
                            </div>
                        </div>

                        <?php
                            include 'sections/html-export-log.php';
                            include 'sections/creating-zip-logs.php';
                            include 'sections/uploading-files-to-ftp-logs.php';
                            include 'sections/footer-buttons.php';
                        ?>

                    </div>

                    <!--Right sidebar-->
                    <?php
                        include 'sections/right-sidebar.php';
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- This templates was made by Colorlib (https://colorlib.com) -->
<?php
    include 'sections/ftp-path-popup.php';
    include 'sections/hidden-fields-and-js.php';
?>

