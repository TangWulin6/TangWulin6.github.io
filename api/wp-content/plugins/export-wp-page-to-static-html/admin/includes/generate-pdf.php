<?php

namespace ExportHtmlAdmin\Generate_PDF_Button;
if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Generate_PDF_Button {
    
    public function __construct() {
        add_action('admin_bar_menu', array($this, 'add_pdf_button'), 100);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        
        add_action('wp_ajax_check_pdf_limit', array($this, 'check_pdf_limit'));
        add_action('wp_ajax_nopriv_check_pdf_limit', array($this, 'check_pdf_limit')); // Optional for guests

        
        add_shortcode('generate_pdf_button', [$this, 'ewpsh_generate_pdf_shortcode']);

        //add_shortcode('generate_pdf_button', 'ewpsh_generate_pdf_shortcode');

        add_action('wp_footer', function() {
            if (!isset($_GET['generate-pdf']) || $_GET['generate-pdf'] !== 'true') {
                return;
            }
        
            ?><style>
            .pdf-download-modal {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
        
            .pdf-download-modal-content {
                background: #fff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                text-align: center;
                max-width: 450px;
                width: 90%;
                position: relative;
                animation: fadeIn 0.3s ease;
            }
        
            .pdf-download-modal-content h2 {
                margin-top: 0;
                font-size: 22px;
                color: #333;
            }
        
            .pdf-download-modal-content p {
                font-size: 15px;
                color: #555;
                margin: 15px 0 25px;
                line-height: 1.5;
            }
        
            .pdf-download-modal-content .modal-actions {
                display: flex;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
            }
        
            .pdf-download-modal-content a.button-pro {
                background: #0073aa;
                color: #fff;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
                transition: background 0.2s ease;
            }
        
            .pdf-download-modal-content a.button-pro:hover {
                background: #005d8f;
            }
        
            .pdf-download-modal-content button.modal-close {
                background: #e0e0e0;
                border: none;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
                color: #333;
                cursor: pointer;
            }
        
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
        </style>
        
        <div class="pdf-download-modal" id="pdf-download-modal">
            <div class="pdf-download-modal-content">

                <?php if ($this->ewpptsh_can_generate_pdf_today()) : ?>
                    <h2><?php esc_html_e('Generating PDF...', 'export-wp-page-to-static-html'); ?></h2>
                    <p><?php esc_html_e('Please wait while your file is being prepared.', 'export-wp-page-to-static-html'); ?></p>
                <?php else : ?>
                    <h2><?php esc_html_e('Daily Limit Reached', 'export-wp-page-to-static-html'); ?></h2>
                    <p><?php esc_html_e("You've already generated 2 PDFs today. Come back tomorrow or upgrade to Pro for unlimited downloads.", 'export-wp-page-to-static-html'); ?></p>
                    <div class="modal-actions">
                        <a href="https://myrecorp.com/product/export-wp-pages-to-static-html-css-pro/?clk=wp&a=exceeded-pdf" target="_blank" class="button-pro"><?php esc_html_e('Upgrade to Pro', 'export-wp-page-to-static-html'); ?></a>
                        <button class="modal-close" onclick="document.getElementById('pdf-download-modal').style.display='none'"><?php esc_html_e('Close', 'export-wp-page-to-static-html'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
            <?php
        });
        
        
    }


    /**
     * Add the "Generate PDF" button to the admin bar
     */
    public function add_pdf_button($wp_admin_bar) {
        if (!is_admin_bar_showing()||is_admin()) {
            return;
        }

        
        // Get the current user
        $current_user = wp_get_current_user();
        $allowed_roles = get_option('_user_roles_can_generate_pdf', []);
        $allowed_roles[] = 'administrator'; // Ensure admin can always generate PDF

        if (in_array('guest', $allowed_roles)) {
            $can_generate = true;
        }
        else{
            // Check if any of the current user's roles are in the allowed list
            $can_generate = false;
            foreach ($current_user->roles as $role) {
                if (in_array($role, $allowed_roles)) {
                    $can_generate = true;
                    break;
                }
            }
        }
        // Show button only if allowed
        if (!$can_generate) {
            return;
        }


        $wp_admin_bar->add_node(array(
            'id'    => 'generate-pdf',
            'title' => '<img src="'.EWPPTSH_PLUGIN_DIR_URL.'/admin/images/down3.png">Generate PDF',
            'href'  => '?generate-pdf=true',
            'meta'  => array(
                'class' => 'download-pdf-button',
                'title' => 'Generate PDF',
            ),
        ));
    }

    public function ewpptsh_can_generate_pdf_today() {
        $key = 'ewpptsh_global_pdf_limit';
        $today = date('Y-m-d');
    
        $data = get_transient($key);
    
        if ( false && $data && isset($data['date']) && $data['date'] === $today) {
            if ($data['count'] >= 2) {
                return false;
            }
        }
    
        return true;
    }
    
    

    public function ewpsh_generate_pdf_shortcode($atts) {

        
        // Get the current user
        // $current_user = wp_get_current_user();
        // $allowed_roles = get_option('_user_roles_can_generate_pdf', []);
        // $allowed_roles[] = 'administrator'; // Ensure admin can always generate PDF

        // if (in_array('guest', $allowed_roles)) {
        //     $can_generate = true;
        // }
        // else{
        //     // Check if any of the current user's roles are in the allowed list
        //     $can_generate = false;
        //     foreach ($current_user->roles as $role) {
        //         if (in_array($role, $allowed_roles)) {
        //             $can_generate = true;
        //             break;
        //         }
        //     }
        // }
        // // Show button only if allowed
        // if (!$can_generate) {
        //     return;
        // }

        $atts = shortcode_atts(array(
            'name' => 'Generate PDF', // Optional custom button text
        ), $atts, 'generate_pdf_button');
    
        $button_text = !empty($atts['button_name']) ? esc_html($atts['button_name']) : esc_html__('Generate PDF', 'export-wp-page-to-static-html');
    
        return '<a id="ewpsh_generate_pdf" href="?generate-pdf=true">' . $button_text . '</a>';
    }
    

    /**
     * Enqueue JavaScript and CSS
     */
    public function enqueue_scripts() {
        if (!is_admin_bar_showing()) return; // Only load for logged-in users with the admin bar
        $generate_pdf = isset($_GET['generate-pdf']) ? sanitize_text_field($_GET['generate-pdf']) : '';

        
            
        // Enqueue custom CSS
        wp_add_inline_style('admin-bar', '
            #wpadminbar .download-pdf-button a {
                display: flex !important;
                align-items: center;
                gap: 4px;
                background: linear-gradient(45deg, #ff416c, #ff4b2b) !important;
                color: white !important;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                text-decoration: none;
                transition: all 0.3s ease-in-out;
                box-shadow: 0px 4px 10px rgba(255, 75, 43, 0.5);
                height: 26px !important;
                position: relative;
                top: 4px;
            }

            #wpadminbar .download-pdf-button a:hover {
                background: linear-gradient(45deg, #ff4b2b, #ff416c) !important;
                transform: scale(1.05);
                box-shadow: 0px 6px 15px rgba(255, 75, 43, 0.7) !important;
            }
        ');

        if ($generate_pdf !== 'true') {
            return; // Only enqueue on the specific page
        }

        // Allow for Pro version to skip this (if needed)
        if (!$this->ewpptsh_can_generate_pdf_today()) {
            return;
        }
            
        // Enqueue jsPDF and html2canvas
        wp_enqueue_script('html2pdf', EWPPTSH_PLUGIN_DIR_URL.'/admin/js/pdf-making/html2pdf.bundle.min.js', array(), null, true);
        wp_enqueue_script('jspdf', EWPPTSH_PLUGIN_DIR_URL.'/admin/js/pdf-making/jspdf.umd.min.js', array(), null, true);
        wp_enqueue_script('pdf-custom-js', EWPPTSH_PLUGIN_DIR_URL.'/admin/js/pdf-making/pdf-making-custom.js', array('jspdf'), null, true);
        

        // Ensure this runs only on the frontend (avoid errors in the admin panel)
        if (!is_admin()) {
            $page_name = get_permalink(); // Get the actual page/post slug
            if (!$page_name) {
                $page_name = 'document'; // Fallback name
            }

            wp_localize_script('pdf-custom-js', 'EWPPTSH_WP_PageData', array('current_page' => $this->get_clean_page_basename()));
            wp_localize_script('pdf-custom-js', 'EWPPTSH_WP_Ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('check_pdf_limit_nonce')
            ));
            
        }
    }

    function get_clean_page_basename() {
        // Get the site URL without protocol (http/https)
        $site_url = str_replace(array('http://', 'https://', 'www.'), '', get_site_url());
        
        $site_url = preg_replace('/[^a-zA-Z0-9-]/', '-', $site_url); // Replace special characters
    
        // Get the current page slug
        $page_slug = get_post_field('post_name', get_queried_object_id());
    
        // If it's the homepage, use a fallback name
        if (is_front_page() || is_home()) {
            $page_slug = 'homepage';
        }
    
        // If there's no slug (e.g., archive/category pages), get the last part of the URL
        if (!$page_slug) {
            $page_slug = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $page_slug = preg_replace('/[^a-zA-Z0-9-]/', '-', $page_slug); // Replace special characters
            if (!$page_slug) {
                $page_slug = 'homepage'; // Final fallback
            }
        }
    
        // Return formatted name like "example.com-pagename"
        return $site_url . '-' . $page_slug;
    }


    public function check_pdf_limit() {
        check_ajax_referer('check_pdf_limit_nonce', 'nonce');
    
        if (!is_user_logged_in()) {
            wp_send_json(array('allow' => false));
        }
    
        $user_id = get_current_user_id();
        $today = date('Y-m-d');
    
        $log = get_user_meta($user_id, 'pdf_export_log', true);
        if (!$log || !is_array($log)) {
            $log = [];
        }
    
        // Reset if date has changed
        if (!isset($log['date']) || $log['date'] !== $today) {
            $log = [
                'date' => $today,
                'count' => 0
            ];
        }
    
        if ($log['count'] >= 2) {
            wp_send_json(['allow' => false]);
        }
    
        // Update count
        $log['count'] += 1;
        update_user_meta($user_id, 'pdf_export_log', $log);
    
        wp_send_json(['allow' => true]);
    }
    

    
}

// Initialize generating pdf functionality
new Generate_PDF_Button();
