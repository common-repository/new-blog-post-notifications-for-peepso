(function($) {

    $(document).ready(function() {

        /* * * Hide Featured Images child options if disabled * * */
        var $images = $("input[name='blogposts_profile_featured_image_enable']");
        if ($images.size() > 0) {

            $images.on("change", function() {
                if ($(this).is(":checked")) {
                    // Show child fields
                    $('#field_blogposts_profile_featured_image_position').fadeIn();
                    $('#field_blogposts_profile_featured_image_height').fadeIn();
                    $('#field_blogposts_profile_featured_image_align').fadeIn();
                    $('#field_blogposts_profile_featured_image_align_vertical').fadeIn();
                    $('#field_blogposts_profile_featured_image_enable_if_empty').fadeIn();
                    $('#field_blogposts_profile_featured_image_position').fadeIn();

                    blogposts_profile_featured_image_height

                } else {
                    // Hide child fields
                    $('#field_blogposts_profile_featured_image_position').fadeOut();
                    $('#field_blogposts_profile_featured_image_height').fadeOut();
                    $('#field_blogposts_profile_featured_image_align').fadeOut();
                    $('#field_blogposts_profile_featured_image_align_vertical').fadeOut();
                    $('#field_blogposts_profile_featured_image_enable_if_empty').fadeOut();
                    $('#field_blogposts_profile_featured_image_position').fadeOut();

                }
            }).trigger("change");
        }

        /* * * Hide Two Column child options if disabled * * */
        var $twocolumn = $("input[name='blogposts_profile_two_column_enable']");
        if ($twocolumn.size() > 0) {
            $twocolumn.on("change", function() {
                if ($(this).is(":checked")) {
                    // Show child fields
                    $('#field_blogposts_profile_two_column_height').fadeIn();
                    $('#field_blogposts_profile_two_column_enable_overflow_hide').fadeIn();
                } else {
                    // Hide child fields
                    $('#field_blogposts_profile_two_column_height').fadeOut();
                    $('#field_blogposts_profile_two_column_enable_overflow_hide').fadeOut();
                }
            }).trigger("change");
        }

        /* * * Hide Template Overrides child options if disabled * * */
        var $twocolumn = $("input[name='blogposts_profile_template_overrides']");
        if ($twocolumn.size() > 0) {
            $twocolumn.on("change", function() {
                if ($(this).is(":checked")) {
                    // Show child fields
                    $('#field_blogposts_profile_template_overrides_description').fadeIn();
                } else {
                    // Hide child fields
                    $('#field_blogposts_profile_template_overrides_description').fadeOut();
                }
            }).trigger("change");
        }





    });



})(jQuery);
