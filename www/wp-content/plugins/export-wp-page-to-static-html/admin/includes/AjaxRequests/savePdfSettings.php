<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\savePdfSettings;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{
    private $ajax;
    public function __construct($ajax)
    {
        /*Initialize Ajax savePdfSettings*/
        add_action('wp_ajax_savePdfSettings', array( $this, 'savePdfSettings' ));
        $this->ajax = $ajax;
    }

    /**
     * Ajax action name: savePdfSettings
     * @since    2.0.0
     * @access   public
     * @return json
     */
    public function savePdfSettings(){
        $user_roles = isset($_POST['userRolesArray']) && is_array($_POST['userRolesArray']) ? array_map('sanitize_text_field', $_POST['userRolesArray']) : array();

        if (!$this->ajax->nonceCheck()){
            echo wp_json_encode(array('success' => false, 'status' => 'nonce_verify_error', 'response' => ''));
            die();
        }

        update_option('_user_roles_can_generate_pdf', $user_roles);

        echo wp_json_encode(array('success' => true, 'status' => 'success', 'response' => $user_roles));

        die();
    }


}