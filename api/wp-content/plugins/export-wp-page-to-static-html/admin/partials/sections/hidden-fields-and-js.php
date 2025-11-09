
<div id="cancel_ftp_process" type="hidden" value="false"></div>
<input id="is_paused" type="hidden" value="false">
<input class="export_id" type="hidden" value="">

<script>

    var $ = jQuery;

    <?php

    if (!empty($query->posts)) {
    foreach ($query->posts as $key => $post) {
    $post_id = $post->ID;
    $post_title = $post->post_title;
    ?>

    <?php
    }
    }
    ?>

    function rc_select2_is_not_ajax() {
        var selectSimple = $('.js-select-simple');

        selectSimple.each(function () {
            var that = $(this);
            var selectBox = that.find('select');
            var selectDropdown = that.find('.select-dropdown');

            // Ensure multiple-select is enabled in case the markup forgot it
            if (!selectBox.prop('multiple')) {
            selectBox.prop('multiple', true);
            }

            selectBox.select2({
            placeholder: "Choose up to 3 pages",
            dropdownParent: selectDropdown,
            maximumSelectionLength: 3,
            language: {
                maximumSelected: function (args) {
                return "Maximum 3 pages can be selected. Upgrade to pro version to select unlimited pages.";
                }
            },
            matcher: function (params, option) {
                var searchTerm = $.trim(params.term);
                if (searchTerm === '') { return option; }
                if (typeof option.text === 'undefined') { return null; }

                var searchTermLower = searchTerm.toLowerCase();
                var searchFunction = function (thisOption, term) {
                return thisOption.text.toLowerCase().indexOf(term) > -1 ||
                    (thisOption.id && thisOption.id.toLowerCase().indexOf(term) > -1);
                };

                if (!option.children) {
                return searchFunction(option, searchTermLower) ? option : null;
                }

                option.children = option.children.filter(function (childOption) {
                return searchFunction(childOption, searchTermLower);
                });
                return option;
            },
            templateResult: function (idioma) {
                var permalink = $(idioma.element).attr('permalink');
                return $("<span permalink='" + permalink + "'>" + idioma.text + "</span>");
            }
            });

            // Extra guard: if user somehow gets past the limit, revert and notify
            selectBox.on('select2:select', function (e) {
            var selected = $(this).val() || [];
            if (selected.length > 3) {
                // Undo the last selection
                $(this).find('option[value="' + e.params.data.id + '"]').prop('selected', false);
                $(this).trigger('change.select2');
                // Simple notice (replace with your own toast/UI if desired)
                alert("Maximum 3 pages can be selected. Upgrade to pro version to select unlimited pages.");
            }
            });
        });
    }


    $(document).ready(function(){
        rc_select2_is_not_ajax();
    });
</script>




