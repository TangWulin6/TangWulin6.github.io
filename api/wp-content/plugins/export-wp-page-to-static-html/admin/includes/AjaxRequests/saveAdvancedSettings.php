<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\saveAdvancedSettings;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax saveAdvancedSettings*/
        add_action('wp_ajax_saveAdvancedSettings', array( $this, 'saveAdvancedSettings' ));

    }

    /**
     * Ajax action name: saveAdvancedSettings
     * @since    2.0.0
     * @access   public
     * @return json
     */
    public function saveAdvancedSettings(){
        $createIndexOnSinglePage = isset($_POST['createIndexOnSinglePage']) && sanitize_key($_POST['createIndexOnSinglePage']) == 'true'? 'on': 'off';
        $saveAllAssetsToSpecificDir = isset($_POST['saveAllAssetsToSpecificDir']) && sanitize_key($_POST['saveAllAssetsToSpecificDir']) == 'true'? 'on': 'off';
        $keepSameName = isset($_POST['keepSameName']) && sanitize_key($_POST['keepSameName']) == '1'? 'on': 'off';
        $addContentsToTheHeader = isset($_POST['addContentsToTheHeader']) ? sanitize_textarea_field($_POST['addContentsToTheHeader']) : "";
        $addContentsToTheFooter = isset($_POST['addContentsToTheFooter']) ? sanitize_textarea_field($_POST['addContentsToTheFooter']) : "";

        $searchFor = isset($_POST['searchFor']) ? sanitize_textarea_field($_POST['searchFor']) : "";
        $replaceWith = isset($_POST['replaceWith']) ? sanitize_textarea_field($_POST['replaceWith']) : "";

        $excludeUrls = isset($_POST['excludeUrls']) ? sanitize_textarea_field($_POST['excludeUrls']) : "";
        $user_roles = isset($_POST['userRolesArray']) && is_array($_POST['userRolesArray']) ? array_map('sanitize_text_field', $_POST['userRolesArray']) : array();


        \rcCheckNonce();
        update_option('rcExportHtmlCreateIndexOnSinglePage', $createIndexOnSinglePage);
        update_option('rcExportHtmlSaveAllAssetsToSpecificDir', $saveAllAssetsToSpecificDir);
        update_option('rcExportHtmlKeepSameName', $keepSameName);
        update_option('rcExportHtmlExcludeUrls', $excludeUrls);
        update_option('rcExportHtmlAddContentsToTheHeader', $addContentsToTheHeader);
        update_option('rcExportHtmlAddContentsToTheFooter', $addContentsToTheFooter);

        update_option('rcExportHtmlSearchFor', $searchFor);
        update_option('rcExportHtmlReplaceWith', $replaceWith);
        update_option('wpptsh_user_roles', $user_roles);

        echo json_encode(array('success' => true, 'status' => 'success', 'response' => ''));

        die();
    }


}