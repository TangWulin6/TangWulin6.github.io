<?php

namespace ExportHtmlAdmin\EWPPTH_AjaxRequests;
/**
 * Class name: EWPPTH_AjaxRequests
 */
class EWPPTH_AjaxRequests
{

    public function __construct()
    {
        $this->initAjaxRequestsFiles();
        $this->initAjaxRequestsClass();
    }

    public function initAjaxRequestsFiles()
    {
        include 'AjaxRequests/requestForWpPageToStaticHtml.php';
        include 'AjaxRequests/seeLogsInDetails.php';
        include 'AjaxRequests/exportLogPercentage.php';
        include 'AjaxRequests/searchPosts.php';
        include 'AjaxRequests/checkExportingProcessOnSettingsPageLoad.php';
        include 'AjaxRequests/deleteExportedZipFile.php';
        include 'AjaxRequests/exploreFtpDirectory.php';
        include 'AjaxRequests/getFtpDirFileList.php';
        include 'AjaxRequests/checkFtpConnectionStatus.php';
        include 'AjaxRequests/cancelRcExportProcess.php';
        include 'AjaxRequests/saveAdvancedSettings.php';
        include 'AjaxRequests/dismiss_export_html_notice.php';
        include 'AjaxRequests/rc_html_export_files_action.php';
        include 'AjaxRequests/pause_and_resume.php';
        include 'AjaxRequests/get_wp_posts.php';
        include 'AjaxRequests/increament-pdf-count.php';
        include 'AjaxRequests/assetsExporter.php';
        include 'AjaxRequests/pageExporter.php';

        include 'Zip.php';

    }

    public function initAjaxRequestsClass()
    {
        new seeLogsInDetails\initAjax;
        new exportLogPercentage\initAjax;
        new searchPosts\initAjax;
        new requestForWpPageToStaticHtml\initAjax;
        new checkExportingProcessOnSettingsPageLoad\initAjax;
        new deleteExportedZipFile\initAjax;
        new getFtpDirFileList\initAjax;
        new exploreFtpDirectory\initAjax;
        new checkFtpConnectionStatus\initAjax;
        new cancelRcExportProcess\initAjax;
        new saveAdvancedSettings\initAjax;
        new dismissExportHtmlNotice\initAjax;
        new rcHtmlExportFilesAction\initAjax;
        new rcExportSetPause\initAjax;
        new getWpPosts\initAjax;
        new increamentPdfCount\initAjax;
        new assetsExporter\initAjax;
        new pageExporter\initAjax;

    }
    public function nonceCheck()
    {
        $nonce = isset($_REQUEST['rc_nonce']) ? sanitize_text_field($_REQUEST['rc_nonce']) : '';
        if (!wp_verify_nonce( $nonce, "rc-nonce" )) {
            return false;
        }

        require( ABSPATH . WPINC . '/pluggable.php' );
        $capabilities = \get_option('wpptsh_user_roles',array('administrator'));

        if (!empty($capabilities)){
            foreach ($capabilities as $cap) {
                if (current_user_can($cap)){
                    return true;
                    break;
                }
            }
        }
        if (current_user_can('administrator')){
            return true;
        }
        return false;
    }
}

new EWPPTH_AjaxRequests;


