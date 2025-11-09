<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\rcHtmlExportFilesAction;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax
{

    public function __construct()
    {
        /*Initialize Ajax cancel_rc_html_export_process*/
        add_action('wp_ajax_rc_html_export_files_action', array( $this, 'rc_html_export_files_action' ));
    }


    /**
     * Ajax action name: rc_html_export_files_action
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function rc_html_export_files_action(){
        $files_action = isset($_POST['files_action']) ? sanitize_key($_POST['files_action']) : "";
        $fileIds = isset($_POST['fileIds']) ? (array) ($_POST['fileIds']) : "";

        $fileIds = array_map( 'sanitize_text_field', $fileIds );

        \rcCheckNonce();

        $upload_dir = wp_upload_dir()['basedir'] . '/exported_html_files/';

        if (!empty($fileIds)){
            foreach ($fileIds as $fileName) {
                $fname = basename( wp_unslash( $fileName ) );         // collapse traversal
                $fname = sanitize_file_name( $fname );
                $path  = wp_normalize_path( $upload_dir . $fname );
                $realBase = realpath($upload_dir);
                $realPath = realpath($path);
                if (!$realPath || strpos($realPath, $realBase) !== 0) {
                    continue; // skip invalid
                }

                if ($files_action == "remove"){
                    if (file_exists($upload_dir.$fileName)) {
                        $this->show_file($fileName);
                        @unlink($upload_dir.$fileName);
                    }
                }
                elseif ($files_action == "hide"){
                    $this->hide_file($fileName);
                }
                elseif ($files_action == "visible"){
                    $this->show_file($fileName);
                }
            }
        }




        echo json_encode(array('success' => 'true', 'status' => 'success', 'hidden_files' => get_option('rcwph_hidden_files')));

        die();
    }

    public function hide_file($filename){
        $rcwph_files_hide = get_option('rcwph_hidden_files');
        if (empty($rcwph_files_hide)){
            $rcwph_files_hide = array();
        }

        if (!in_array($filename, $rcwph_files_hide)){
            $rcwph_files_hide[] = $filename;
        }

        update_option('rcwph_hidden_files', $rcwph_files_hide);
    }

    public function show_file($filename){
        $rcwph_files_hide = get_option('rcwph_hidden_files');
        if (!empty($rcwph_files_hide)){
            foreach ($rcwph_files_hide as $key => $item) {
                if ($item == $filename){
                    unset($rcwph_files_hide[$key]);
                }
            }
        }


        update_option('rcwph_hidden_files', $rcwph_files_hide);
    }

}
