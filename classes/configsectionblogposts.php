<?php

class PeepSoConfigSectionBlogposts extends PeepSoConfigSectionAbstract
{
	// Builds the groups array
	public function register_config_groups()
	{
		wp_register_script('peepso-blogposts-config', plugin_dir_url( __FILE__ ).('../assets/js/blogposts-config.js'),
			array('jquery'), PeepSo::PLUGIN_VERSION, TRUE);

		wp_enqueue_script('peepso-blogposts-config');

		if(isset($_GET['admin_tutorial_reset'])) {
			delete_user_meta(PeepSo::get_user_id(), 'peepso_blogposts_admin_tutorial_hide');
			PeepSo::redirect(admin_url().'admin.php?page=peepso_config&tab=blogposts');
		}

		if(isset($_GET['admin_tutorial_hide'])) {
			add_user_meta(PeepSo::get_user_id(), 'peepso_blogposts_admin_tutorial_hide', 1, TRUE);
			PeepSo::redirect(admin_url().'admin.php?page=peepso_config&tab=blogposts');
		}

		// display the admin tutorial unless this user has already hidden it
		if(1 != get_user_meta(PeepSo::get_user_id(), 'peepso_blogposts_admin_tutorial_hide', TRUE)) {
			ob_start();
			PeepSoTemplate::exec_template('blogposts', 'admin_tutorial');

			$peepso_admin = PeepSoAdmin::get_instance();
			$peepso_admin->add_notice(ob_get_clean(), '');
		}

		$this->context='left';
		$this->group_profile();
		$this->group_acknowledgements();


		$this->context='right';
		$this->group_activity();
		$this->group_post_types();
	}

	/**
	 * General Settings Box
	 */
	private function group_profile()
	{



	/* * * GENERAL * * */

		$this->args('descript', __('Show "Blog Posts" tab in user profiles','peepsoblogposts'));
		$this->set_field(
			'blogposts_profile_enable',
			__('Enabled', 'peepsoblogposts'),
			'yesno_switch'
		);



	/* * * CONTENT * * */

		$this->set_field(
			'blogposts_profile_content_separator',
			__('Post content', 'peepsoblogposts'),
			'separator'
		);

		$this->args('int', TRUE);
		$this->args('default', 50);
		$this->args('descript', 'How many words of the post excerpt (or post content) to show. Leave empty to hide the content completely.');
		// Once again the args will be included automatically. Note that args set before previous field are gone
		$this->set_field(
			'blogposts_profile_content_length',
			__('Content length', 'peepsoblogposts'),
			'text'
		);

		// DATE
		$this->args('descript','Put the date above/below post content, or disable it completely.');
		$options = array(
			'top' => __('above content', 'peepsoblogposts'),
			'bottom' => __('below content', 'peepsoblogposts'),
			'disable' => __('hidden', 'peepsoblogposts'),
		);
		$this->args('options', $options);
		$this->set_field(
			'blogposts_profile_date_position',
			__('Show date', 'peepsoblogposts'),
			'select'
		);



	/* * * TWO COLUMNS  * * */

		$this->set_field(
			'blogposts_profile_two_columns_separator',
			__('Two column layout', 'peepsoblogposts'),
			'separator'
		);

		// TC ENABLE
		$this->set_field(
			'blogposts_profile_two_column_enable',
			__('Two column layout', 'peepsoblogposts'),
			'yesno_switch'
		);

		// TC HEIGHT
		$this->args('int', TRUE);
		$this->args('default', 350);
		$this->args('descript', '');
		$this->set_field(
			'blogposts_profile_two_column_height',
			__('Box height (px)', 'peepsoblogposts'),
			'text'
		);

		// TC OVERFLOW HIDDEN
		$this->args('descript', '');
		$this->args('default', 1);
		$this->args('descript', __('Recommended - maintains the layout in case of content exceeding size of the box'));
		$this->set_field(
			'blogposts_profile_two_column_enable_overflow_hide',
			__('Clip long content', 'peepsoblogposts'),
			'yesno_switch'
		);



	/* * * FEATURED IMAGES  * * */

		$this->set_field(
			'blogposts_profile_featured_images_separator',
			__('Featured images', 'peepsoblogposts'),
			'separator'
		);

		// FI ENABLE
		#$this->args('descript', __('Will display Featured Images if available','peepsoblogposts'));
		$this->set_field(
			'blogposts_profile_featured_image_enable',
			__('Featured images', 'peepsoblogposts'),
			'yesno_switch'
		);


		// FI POSITION
		$this->args('descript','');
		$options = array(
			'top' => __('top', 'peepsoblogposts'),
			'left' => __('left', 'peepsoblogposts'),
			'right' => __('right', 'peepsoblogposts'),
		);
		$this->args('options', $options);
		$this->set_field(
			'blogposts_profile_featured_image_position',
			__('Position', 'peepsoblogposts'),
			'select'
		);

		// FI HEIGTH
		$this->args('int', TRUE);
		$this->args('default', 150);
		$this->args('descript', __('The image will be a square if not placed on top.'));
		$this->set_field(
			'blogposts_profile_featured_image_height',
			__('Height (px)', 'peepsoblogposts'),
			'text'
		);

		// FI HALIGN
		$options = array(
			'center' => __('center', 'peepsoblogposts'),
			'left' => __('left', 'peepsoblogposts'),
			'right' => __('right', 'peepsoblogposts'),
		);

		$this->args('options', $options);
		$this->args('descript','How to align the image if it\'s wider than the box.');
		$this->set_field(
			'blogposts_profile_featured_image_align',
			__('Horizontal align', 'peepsoblogposts'),
			'select'
		);


		// FI VALIGN
		$options = array(
			'center' => __('center', 'peepsoblogposts'),
			'top' => __('top', 'peepsoblogposts'),
			'bottom' => __('bottom', 'peepsoblogposts'),
		);

		$this->args('options', $options);
		$this->args('descript','How to align the image if it\'s taller than the box.');
		$this->set_field(
			'blogposts_profile_featured_image_align_vertical',
			__('Vertical align', 'peepsoblogposts'),
			'select'
		);

		$this->args('descript', __('Will display an empty "placeholder" box if an image is not found. Recommended to maintain the layout in case of missing images.'));
		$this->set_field(
			'blogposts_profile_featured_image_enable_if_empty',
			__('Placeholder', 'peepsoblogposts'),
			'yesno_switch'
		);

	/* * * ADVANCED  * * */

		$this->set_field(
			'blogposts_profile_template_overrides_separator',
			__('Advanced', 'peepsoblogposts'),
			'separator'
		);

		// ADV TEMPLATE OVERRIDES
		$this->set_field(
			'blogposts_profile_template_overrides',
			__('Use template overrides', 'peepsoblogposts'),
			'yesno_switch'
		);


		$path = PeepSo::get_peepso_dir().'peepso-blogposts/templates/blogposts/';
		$path_wide = $path.'blogpost_wide.php';
		$path_half = $path.'blogpost_half.php';

		$this->set_field(
			'blogposts_profile_template_overrides_description',
			sprintf(__("
You can now create your own template files.<br/>
Please remember, that you are doing this at <b>your own resposibility</b> and that some features and options might stop working.<br>
The original files can be found in the \"templates\" directory of this plugin.
<br><br>

You have to create the files in these exact locations:<br/><pre>Full width (single column view):\n%s\n\nHalf width (two column view):\n%s</pre>", 'peepsoblogposts'), $path_wide, $path_half),
			'message'
		);



		/* * * GROUP * * */

		$this->set_group(
			'blogposts_general',
			__('Profiles Integration', 'peepsoblogposts')
		);
	}

	/**
	 * Acknowledgements Box
	 */
	private function group_acknowledgements()
	{

		$this->set_field(
			'reactions_acknowledgements1_description',
			'PeepSo Blog Posts was developed with love at <a href="http://mattsplugins.io" target="_blank">Matt\'s plugins</a> by <a href="http://jwr.sk" target="_blank">Matt Jaworski</a>.',
			'message'
		);


		$this->set_field(
			'reactions_acknowledgements3_description',
			'Base PeepSo API library based on <a href="https://github.com/PeepSo/peepso-tools-helloworld" target="_blank">PeepSo Hello World</a> courtesy of <a href="http://peepso.com" target="_blank">PeepSo, Inc</a>.',
			'message'
		);

		$this->set_group(
			'reactions_group_acknowledgements',
			__('Plugin Information', 'peepsoreactions')
		);
	}

	/**
	 * General Settings Box
	 */
	private function group_activity()
	{
		$this->set_field(
			'blogposts_activity_enable',
			__('Enable Activity Stream Integration', 'peepsoblogposts'),
			'yesno_switch'
		);

		$privacy = PeepSoPrivacy::get_instance();
		$privacy_settings = apply_filters('peepso_privacy_access_levels', $privacy->get_access_settings());

		$options = array();

		foreach($privacy_settings as $key => $value) {
			$options[$key] = $value['label'];
		}

		$this->args('options', $options);

		$this->set_field(
			'blogposts_activity_privacy',
			__('Default privacy', 'peepsoblogposts'),
			'select'
		);

		$this->set_group(
			'blogposts_general',
			__('Activity Stream Integration', 'peepsoblogposts')
		);
	}

	private function group_post_types()
	{
		$post_types = PeepSoBlogPosts::get_post_types();

		$message = 	__('Enable the post types you want to be handled by this integration.','peepsoblogposts') . '<br/>' .
			__('Only post types presently registered are visible.','peepsoblogposts') . '<br/>' .
			__('You can override the <i>action text</i> for each post type  with custom wording.','peepsoblogposts');
		$this->set_field(
			'post_types_message',
			$message,
			'message'
		);

		foreach($post_types as $post_type) {
			$key = 'blogposts_activity_type_'.$post_type;
			$key_text	 = $key.'_text';

			$this->set_field(
				$post_type.'_separator',
				$post_type,
				'separator'
			);

			$this->set_field(
				$key,
				'Enable',
				'yesno_switch'
			);

			$this->args('default', PeepSo::get_option('blogposts_activity_type_post_text_default'));

			$this->set_field(
				$key_text,
				'Action text',
				'text'
			);
		}

		$this->set_group(
			'blogposts_activity_post_types',
			__('Activity Stream Integration - Post types', 'peepsoblogposts')
		);
	}
}