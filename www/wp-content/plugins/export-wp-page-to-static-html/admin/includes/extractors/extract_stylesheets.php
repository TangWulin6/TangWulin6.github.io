<?php

namespace ExportHtmlAdmin\extract_stylesheets;
class extract_stylesheets
{

    private  $admin;

    public function __construct($admin)
    {
        $this->admin = $admin;
    }


    /**
     * @since 2.0.0
     * @param string $url
     * @return array
     */
    public function process($elements, $url = '')
    {
        wpptsh_error_log('Before stylesheets');

        if ($this->admin->is_cancel_command_found()) {
            wpptsh_error_log("❌ Cancel command found! Exiting during get_stylesheets init.");
            return;
        }

        wpptsh_error_log('After stylesheets cancel command check');

        $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
        $path_to_dot = $this->admin->rc_path_to_dot($url, true, true);
        $src = $this->admin->site_data;

        foreach ($elements as $link) {
                // Check if the cancel command is found for the admin and exit if true
                if ($this->admin->is_cancel_command_found()) {
                    exit;
                }
                if(isset($link->href) && !empty($link->href) ){
                    $href_link = $link->href;
                    $href_link = $this->admin->clean_url($href_link, $url);

                    $host = $this->admin->get_host($href_link);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $href_link);
                    if( !empty($href_link) && !empty($host) && strpos($href_link, '.css')!==false && strpos($url, $host)!==false && !$exclude_url){

                        // $this->admin->currently_exporting_url($href_link);

                        // $newlyCreatedBasename = $this->save_stylesheet($href_link, $url);
                        
                        $middle_p = $this->admin->middle_path_for_filename($href_link);
                        $basename = $this->admin->url_to_basename($href_link, false, '.css');

                        $this->admin->add_urls_log($href_link, $url, 'css', 0, $middle_p . $basename);

                        //if(!$saveAllAssetsToSpecificDir){
                            $link->href = $path_to_dot . 'css/' . $middle_p . $basename;
                        // }
                        // else{
                        //     $link->href = $path_to_dot .'css/'. $basename;
                        // }

                    }
                }
            }
            $this->admin->site_data = $src;
        }

    

    /**
     * @since 2.0.0
     * @param string $stylesheet_url
     * @param string $found_on
     * @return false|string
     */
    public function save_stylesheet($stylesheet_url = "", $found_on = "", $file_name = "")
    {
        wpptsh_error_log("Start: save_stylesheet | stylesheet_url: $stylesheet_url | found_on: $found_on");

        $pathname_fonts = $this->admin->getFontsPath();

        $pathname_css = $this->admin->getCssPath();
        wpptsh_error_log("****pathname_css path: $pathname_css");
        $pathname_images = $this->admin->getImgPath();
        $host = $this->admin->get_host($found_on);
        $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->admin->getExportTempDir();
        $keepSameName = $this->admin->getKeepSameName();

        $m_basename = $this->admin->middle_path_for_filename($stylesheet_url);
        $basename = $this->admin->url_to_basename($stylesheet_url);
        wpptsh_error_log("m_basename: $m_basename | basename: $basename");

        if (true ||!$this->admin->rc_is_link_already_generated($stylesheet_url)) {
            wpptsh_error_log("Link not already generated: $stylesheet_url");

            $this->admin->update_export_log($stylesheet_url, 'copying', '');
            $data = $this->admin->get_url_data($stylesheet_url);
            
            wpptsh_error_log("Fetched data for stylesheet");
            // if (is_array($data) && isset($data['response_code'])) {
            //     $this->admin->update_urls_log($stylesheet_url, 1);
            //     return;
            // }

            preg_match_all("/(?<=url\().*?(?=\))/", $data, $images_links);
            wpptsh_error_log("Found image links in CSS: " . json_encode($images_links));
            wpptsh_error_log("Total found image/font URLs: " . count($images_links[0]));

            foreach ($images_links as $key => $images) {
        
                $replacements_from = [];
                $replacements_to = [];

                foreach ($images as $image) {
                    if (strpos($image_url, 'data:') !== false || strpos($image_url, 'base64') !== false) {
                        continue;
                    }
                    // wpptsh_error_log("Memory used: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");

                    if ($this->admin->is_cancel_command_found()) {
                        wpptsh_error_log("Cancel command found while processing image: $image");
                        exit;
                    }

                    $newImageUrl = $this->admin->clean_url($image_url, $stylesheet_url);
                    wpptsh_error_log("Resolved image URL: $newImageUrl");

                    $this->admin->currently_exporting_item($image_url);

                    $item_url = $newImageUrl;
                    $this->admin->add_urls_log($item_url, $stylesheet_url, 'cssItem');


                    $url_basename = $this->admin->url_to_basename($item_url);
                    $url_basename = $this->admin->filter_filename($url_basename);
                    wpptsh_error_log("Image base name: $url_basename");

                    if (!$saveAllAssetsToSpecificDir) {
                        $path_to_dot = $this->admin->rc_path_to_dot($item_url);
                    } else {
                        if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
                            $path_to_dot = $this->urlToDot($this->admin->middle_path_for_filename($item_url));
                        } else {
                            $path_to_dot = './../';
                        }
                    }

                    if (strpos($item_url, $host) !== false) {
                        $urlExt = \pathinfo($url_basename, PATHINFO_EXTENSION);
                        $fontExt = array("eot", "woff", "woff2", "ttf", "otf");

                        $my_file = null;
                        $replacement_path = '';

                        if (in_array($urlExt, $fontExt)) {
                            wpptsh_error_log("Font detected: $item_url | Extension: $urlExt");

                            if (!file_exists($pathname_fonts)) {
                                @mkdir($pathname_fonts, 0777, true);
                                wpptsh_error_log("Font directory created: $pathname_fonts");
                            }

                            $my_file = $pathname_fonts . $url_basename;
                            $replacement_path = $path_to_dot . 'fonts/' . $url_basename;
                        } elseif (in_array($urlExt, $this->admin->getImageExtensions())) {
                            wpptsh_error_log("Image detected: $item_url");

                            if (!file_exists($pathname_images)) {
                                @mkdir($pathname_images, 0777, true);
                            }

                            $my_file = $pathname_images . $url_basename;
                            $replacement_path = $path_to_dot . 'images/' . $url_basename;
                        } elseif (strpos($item_url, 'css') !== false) {
                            $my_file = $pathname_css . $url_basename;
                            $replacement_path = $path_to_dot . 'css/' . $url_basename;
                        }

                        // Add replacement to arrays
                        if (!empty($replacement_path)) {
                            $replacements_from[] = $item_url;
                            $replacements_to[] = $replacement_path;
                        }

                        // Save asset file if not already saved
                        if (isset($my_file) && !file_exists($my_file)) {
                            wpptsh_error_log("Saving new asset file: $my_file");
                            $this->admin->update_export_log($item_url, 'copying', '');
                            $this->admin->currently_exporting_item($item_url);
                            $this->admin->add_urls_log($item_url, $found_on, 'css');
                            $this->admin->saveFile($item_url, $my_file);
                        } else {
                            $this->admin->currently_exporting_item($item_url);
                            $this->admin->update_urls_log($item_url, $url_basename, 'new_file_name', 'cssItem', $item_url);
                            $this->admin->update_urls_log($item_url, 1);
                        }
                    }

                    // Replace all in one go
                    $data = \str_replace($replacements_from, $replacements_to, $data);

                }
            }

            // if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
            //     $m_basename = explode('-', $m_basename);
            //     $m_basename = implode('/', $m_basename);
            // }

            // if (strpos($basename, ".css") === false) {
            //     $basename = rand(5000, 9999) . ".css";
            //     $this->admin->update_urls_log($stylesheet_url, $basename, 'new_file_name');
            // }

            // $basename = $this->admin->filter_filename($basename);
            // wpptsh_error_log("Final basename: $basename");

            // if (!empty($m_basename)) {
            //     $my_file = $pathname_css . $m_basename . $basename;
            // } else {
            //     $my_file = $pathname_css . $basename;
            // }

            // if (!$saveAllAssetsToSpecificDir) {
            //     $middle_p = $this->admin->rc_get_url_middle_path_for_assets($stylesheet_url);
            //     if (!file_exists($exportTempDir . '/' . $middle_p)) {
            //         @mkdir($exportTempDir . '/' . $middle_p, 0777, true);
            //     }
            //     $my_file = $exportTempDir . '/' . $middle_p . '/' . $basename;
            // } else {
            //     if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
            //         if (!file_exists($exportTempDir . '/' . $m_basename)) {
            //             @mkdir($pathname_css . $m_basename, 0777, true);
            //         }
            //         $my_file = $pathname_css . $m_basename . $basename;
            //     }
            // }

            $my_file = $pathname_css . $file_name;

            wpptsh_error_log("Before saving new asset file: $my_file");
            if (!file_exists($my_file)) {
                wpptsh_error_log("Saving new asset file: $my_file");

                $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
                @fwrite($handle, $data);
                fclose($handle);
                
                wpptsh_error_log("File written: $my_file");
                $this->admin->update_urls_log($stylesheet_url, 1);
            }

            // if ($saveAllAssetsToSpecificDir && !empty($m_basename)) {
            //     return $m_basename . $basename;
            // }

            // return $basename;
        } 
        
        // else {
        //     wpptsh_error_log("Stylesheet already generated: $stylesheet_url");

        //     if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
        //         $m_basename = explode('-', $m_basename);
        //         $m_basename = implode('/', $m_basename);
        //     }

        //     if (!(strpos($basename, ".") !== false) && $this->admin->get_newly_created_basename_by_url($stylesheet_url) != false) {
        //         return $m_basename . $this->admin->get_newly_created_basename_by_url($stylesheet_url);
        //     }

        //     if ($saveAllAssetsToSpecificDir && !empty($m_basename)) {
        //         return $m_basename . $basename;
        //     }

        //     return $basename;
        // }
    }
}