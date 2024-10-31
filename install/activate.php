<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoBlogPostsInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		#'HELLO_WORLD' => '100',
	);

	public function plugin_activation($is_core = FALSE)
	{
		$defaults = array(
				'blogposts_activity_enable'					=>  0,
				'blogposts_activity_privacy' 				=> 10,
				'blogposts_activity_type_post'				=>	1,
				'blogposts_activity_type_post_text_default'	=> 'wrote a new post',
				'blogposts_activity_type_page_text'			=> 'published a new page',
				'blogposts_activity_type_attachment_text'	=> 'created a new attachment',
				'blogposts_activity_type_revision_text' 		=> 'created a new revision',
				'blogposts_activity_type_nav_menu_item_text'	=> 'created a new menu item',
		);
		// Set some default settings
		$settings = PeepSoConfigSettings::get_instance();

		foreach($defaults as $key=>$value) {
			if(in_array(PeepSo::get_option($key, NULL), array(NULL,''))) {
				$settings->set_option($key, $value);
			}
		}

		parent::plugin_activation();

		return (TRUE);
	}
}