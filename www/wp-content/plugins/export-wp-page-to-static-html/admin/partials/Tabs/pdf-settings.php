
<div class="tab-pane pt-3" id="tabs-6" role="tabpanel">
    <div class="pdf_Settings_section">
    <h2><?php esc_html_e('PDF Settings', 'export-wp-page-to-static-html'); ?></h2>

    <div class="p-t-20">
        <?php if(current_user_can('administrator')): ?>
        <div class="settings-item">
            <label class="label">
                <b><?php esc_html_e('User roles can generate PDF', 'export-wp-page-to-static-html'); ?></b>
            </label>

            <?php

                $selected_user_roles = (array) get_option('_user_roles_can_generate_pdf', array());
                $selected_user_roles = array_map('esc_attr', $selected_user_roles);

                $wp_roles_obj = wp_roles(); // catch it first

                if ( is_object( $wp_roles_obj ) && method_exists( $wp_roles_obj, 'get_names' ) ) {
                    $wp_roles = $wp_roles_obj->get_names();

                    foreach ( $wp_roles as $role => $name ) {
                        if ( $role == "administrator" ) {
                            echo '<label for="roles-for-pdf-administrator" class="checkbox-label roles-for-pdf-user-roles" style="margin-right: 12px;">
                                <input id="roles-for-pdf-administrator" type="checkbox" name="administrator_for_pdf" checked disabled> Administrator
                            </label>';
                        } else {
                            $isChecked = in_array($role, $selected_user_roles) ? 'checked' : '';
                            echo '<label for="roles-for-pdf-'.esc_attr($role).'" class="checkbox-label roles-for-pdf-user-roles" style="margin-right: 12px;">
                                <input id="roles-for-pdf-'.esc_attr($role).'" '.esc_attr($isChecked).' type="checkbox" name="user_roles_for_pdf['.esc_attr($role).']" value="'.esc_attr($role).'"> '.esc_attr($name).'
                            </label>';
                        }
                    }
                } else {
                    echo '<div style="color:red;">Error: User roles could not be loaded properly.</div>';
                }

            $guest_checked = in_array('guest', $selected_user_roles) ? 'checked': '';
            echo '<br><label for="roles-for-pdf-guest" class="checkbox-label roles-for-pdf-user-roles" style="margin-right: 12px;"><input id="roles-for-pdf-guest" type="checkbox" name="user_roles_for_pdf[guest]" value="guest" '.$guest_checked. '> Visitor</label>';

            ?>
            <div style="margin-top: 5px; font-size: 13px;"><i><?php esc_html_e('Select the user roles that have access to generate PDF of a page.', 'export-wp-page-to-static-html'); ?></i></div>
        </div>
        <div class="settings-item pt-3">
            <label class="label">
                <b><?php esc_html_e('Shortcode', 'export-wp-page-to-static-html'); ?></b>
            </label>


            <div style="margin-top: 10px; display: flex; gap: 10px; align-items: center;">
                <input type="text" id="pdf-shortcode" value='[generate_pdf_button name="Generate PDF"]' readonly style="max-width: 365px;flex: 1; padding: 5px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px;" />
                <button type="button" class="button button-secondary" onclick="copyShortcode()">Copy</button>
                <span id="copy-msg" style="display: none; color: green; font-weight: bold;">Copied!</span>
            </div>

            <div style="margin-top: 5px; font-size: 13px;">
                <i><?php esc_html_e('Add this shortcode anywhere to display the generate PDF button.', 'export-wp-page-to-static-html'); ?></i>
            </div>

            
    <button class="btn btn--radius-2 btn--blue m-t-20 btn_save_pdf_settings" type="submit">Save Settings <span class="spinner_x hide_spin"></button>
    <span class="badge badge-success badge_save_settings" style="display: none; padding: 5px">Successfully Saved!</span>


            <script>
                function copyShortcode() {
                    var copyText = document.getElementById("pdf-shortcode");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // For mobile

                    document.execCommand("copy");

                    var msg = document.getElementById("copy-msg");
                    msg.style.display = "inline";

                    // Hide the message after 2 seconds
                    setTimeout(function() {
                        msg.style.display = "none";
                    }, 2000);
                }
            </script>
        </div>


        <?php endif; ?>
    </div>
    </div>
</div>