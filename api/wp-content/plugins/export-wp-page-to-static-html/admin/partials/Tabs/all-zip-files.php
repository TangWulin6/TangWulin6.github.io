<div class="tab-pane" id="tabs-3" role="tabpanel">

    <div class="files_action_select_section">

        <div class="all_zip_files">
            <?php ob_start(); ?>
            <div class="files_action_select_section" style="padding-bottom: 10px;border-bottom: 1px solid #dddddd;margin-bottom: 4px;">
                <input type="checkbox" value="check_all_files" id="check_all_files" style="vertical-align: middle">
                <select name="files_action" id="files_action">
                    <option value=""><?php _e('Select an action', 'export-wp-page-to-static-html'); ?></option>
                    <option value="remove"><?php _e('Remove', 'export-wp-page-to-static-html'); ?></option>
                    <option value="hide"><?php _e('Hide', 'export-wp-page-to-static-html'); ?></option>
                    <option value="visible"><?php _e('Visible', 'export-wp-page-to-static-html'); ?></option>
                </select>
                <button class="btn submit_files_action btn--blue" style="padding: 15px 12px;line-height: 0;vertical-align: middle;border-radius: 4px;font-size: 14px;"><?php _e('Submit', 'export-wp-page-to-static-html'); ?></button>

                <a href="#" class="show_hidden_files" style="float: right;position: relative;top: 6px;"><?php _e('Show hidden files', 'export-wp-page-to-static-html'); ?></a>
            </div>
            <?php

            $c = 0;

            if (!empty($d)) {
                while($file = $d->read()) {
                    if (strpos($file, '.zip')!== false) {
                        $c++;
                        echo '<div class="exported_zip_file '.rcwpth_hidden_class($file).'"><input type="checkbox" value="'.$file.'">'.$c.'. <a class="file_name" href="'.$upload_url.$file.'">'.$file.'</a><span class="delete_zip_file" file_name="'.$file.'"></span></div>';
                    }
                }
            }

            $filesHtml = ob_get_clean();

            if ($c == 0) {
                echo '<div class="files-not-found">Files not found!</div>';
            }
            else{
                echo $filesHtml;
            }
            echo '</div>';
            ?>
        </div>
    </div>