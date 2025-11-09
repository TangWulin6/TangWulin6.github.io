<div class="tab-pane" id="tabs-5" role="tabpanel">
    <div class="p-t-20">
        <label class="checkbox-container full_site m-r-45" for="createIndexOnSinglePage"><?php _e('Create <b>index.html</b> on single page exporting', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="createIndexOnSinglePage" name="createIndexOnSinglePage" <?php echo $createIndexOnSinglePage == "on" ? 'checked' : ''; ?> >
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="p-t-20">
        <label class="checkbox-container m-r-45" for="saveAllAssetsToSpecificDir"><?php _e('Save all assets files to the specific directory (css, js, images, fonts, audios etc).', 'export-wp-page-to-static-html'); ?>
            <input type="checkbox" id="saveAllAssetsToSpecificDir" name="saveAllAssetsToSpecificDir" <?php echo $saveAllAssetsToSpecificDir == "on" ? 'checked' : ''; ?>>
            <span class="checkmark"></span>
        </label>

        <div class="saveAllAssetsToSpecificDir_assets_subsection export_html_sub_settings mt-4"  style="display: <?php echo $saveAllAssetsToSpecificDir ? 'block' : 'none'; ?>">
            <label class="radio-container m-r-45" for="keepSameName"><?php _e('Keep the same name (file will save into year and date directory. Example: 2022/06/filename.png)', 'export-wp-page-to-static-html'); ?>
                <input type="radio" id="keepSameName" name="keepSameName" value="1" <?php echo $keepSameName == "on" ? 'checked' : ''; ?>>
                <span class="checkmark"></span>
            </label>
            <label class="radio-container m-r-45 mt-3" for="saveAllInOneDir"><?php _e('Save all files into the related directory (it will add year and month before the file. Example: 2022-06-filename.png)(<strong>Recommended</strong>)', 'export-wp-page-to-static-html'); ?>
                <input type="radio" id="saveAllInOneDir" name="keepSameName" value="0" <?php echo $keepSameName == "off" ? 'checked' : ''; ?>>
                <span class="checkmark"></span>
            </label>
        </div>
    </div>


    <div class="p-t-20">
        <label class="label m-r-45" for="excludeUrls"><?php _e('<b>Exclude Urls</b>', 'export-wp-page-to-static-html'); ?>
            <br>
            <textarea id="excludeUrls" name="excludeUrls" style="height: 80px; width: 100%"><?php echo $excludeUrls; ?></textarea>
        </label>
    </div>

    <div class="p-t-20">
        <label class="label m-r-45" for="addContentsToTheHeader"><?php _e('<b>Add contents to the header</b>', 'export-wp-page-to-static-html'); ?>
            <br>
            <textarea id="addContentsToTheHeader" name="addContentsToTheHeader" style="height: 80px; width: 100%"><?php echo $addContentsToTheHeader; ?></textarea>
        </label>
    </div>
    <div class="p-t-20">
        <label class="label m-r-45" for="addContentsToTheFooter"><?php _e('<b>Add contents to the footer</b>', 'export-wp-page-to-static-html'); ?>
            <br>
            <textarea id="addContentsToTheFooter" name="addContentsToTheFooter" style="height: 80px; width: 100%"><?php echo $addContentsToTheFooter; ?></textarea>
        </label>
    </div>



    <div class="p-t-20">
        <label class="label m-r-45" for="searchFor"><?php _e('<b>Search for</b>', 'export-wp-page-to-static-html'); ?>
            <br>
            <textarea id="searchFor" name="searchFor" style="height: 80px; width: 100%"><?php echo $searchFor; ?></textarea>
            <small class="dim-text"><?php _e('Description: Enter the text to search for, separated by commas (e.g., term1, term2, term3).', 'export-wp-page-to-static-html'); ?></small>
        </label>
    </div>
    <div class="p-t-20">
        <label class="label m-r-45" for="replaceWith"><?php _e('<b>Replace with</b>', 'export-wp-page-to-static-html'); ?>
            <br>
            <textarea id="replaceWith" name="replaceWith" style="height: 80px; width: 100%"><?php echo $replaceWith; ?></textarea>
            <small class="dim-text"><?php _e('Description: Enter the replacement text, separated by commas (e.g., replacement1, replacement2, replacement3).', 'export-wp-page-to-static-html'); ?></small>
        </label>
    </div>




    <div class="p-t-20">
        <?php if(current_user_can('administrator')): ?>
            <div class="settings-item">
                <label class="label">
                    <b><?php _e('User roles can access', 'export-wp-page-to-static-html'); ?></b>
                </label>

                <?php

$selected_user_roles = (array) get_option('_user_roles_can_generate_pdf', array());
$selected_user_roles = array_map('esc_attr', $selected_user_roles);

$wp_roles = wp_roles(); // no get_names() needed

if ( is_array( $wp_roles ) && !empty( $wp_roles ) ) {
    foreach ( $wp_roles as $role => $name ) {
        if ( $role == "administrator" ) {
            echo '<label for="roles-for-pdf-administrator" class="checkbox-label roles-for-pdf-user-roles" style="margin-right: 12px;">
                <input id="roles-for-pdf-administrator" type="checkbox" name="administrator_for_pdf" checked disabled> Administrator
            </label>';
        } else {
            echo '<label for="roles-for-pdf-'.esc_attr($role).'" class="checkbox-label roles-for-pdf-user-roles" style="margin-right: 12px;">
                <input id="roles-for-pdf-'.esc_attr($role).'" type="checkbox" name="user_roles_for_pdf['.esc_attr($role).']" value="'.esc_attr($role).'" '.checked( in_array($role, $selected_user_roles), true, false ).'> '.esc_html($name).'
            </label>';
        }
    }
} else {
    echo '<div style="color:red;">Error: User roles could not be loaded properly.</div>';
}



                ?>
                <div style="margin-top: 5px; font-size: 13px;"><i><?php _e('Select user roles to access the "Export WP Pages to Static HTML/CSS" option.', 'export-wp-page-to-static-html'); ?></i></div>
            </div>
        <?php endif; ?>
    </div>

    <button class="btn btn--radius-2 btn--blue m-t-20 btn_save_settings" type="submit"><?php _e('Save Settings', 'export-wp-page-to-static-html'); ?> <span class="spinner_x hide_spin"></button>
    <span class="badge badge-success badge_save_settings" style="display: none; padding: 5px"><?php _e('Successfully Saved!', 'export-wp-page-to-static-html'); ?></span>
</div>