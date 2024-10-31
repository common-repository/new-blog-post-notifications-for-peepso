<?php
/**
 * Plugin Name: PeepSo Blog Posts
 * Plugin URI: http://mattsplugins.io
 * Description: Display a list of user Blog Posts in their profile and post to Activity when a new post is published
 * Author: Matt Jaworski
 * Author URI: http://mattsplugins.io
 * Version: 2.1.2
 * Copyright: (c) 2015 Matt Jaworski All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsoblogposts
 * Domain Path: /languages
 *
 * This software contains GPLv2 or later software courtesy of PeepSo.com, Inc
 *
 * PeepSo Blog Posts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * PeepSo Blog Posts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */


class PeepSoBlogPosts
{
	private static $_instance = NULL;

	public $post_types; // @todo might be unused?

	// DO NOT rename these - this is how our wp_post entries are recognized
	const MODULE_ID = 6661;
	const SHORTCODE= 'peepso_postnotify';

	// constants to hook into PeepSo version check
	const PLUGIN_NAME = "PeepSo Blog Posts";
	const PLUGIN_VERSION = '2.1.2';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

	// post types that are known to wreck havoc when enabled
	private static $post_types_blacklist = array(
		'peepso-post',
		'peepso-message',
		'peepso-comment',
		'peepso_user_field',
	);

	/**
	 * constructor, register initial hooks
	 *
	 * @return void
	 */
	private function __construct()
	{
		add_action('peepso_init', array(&$this, 'init'));
		add_action('plugins_loaded', array(&$this, 'load_textdomain'));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'check_peepso'));
		}

		// @todo why doesn't this work in init?
		add_action( 'wp_ajax_peepsoblogposts_user_posts', array(&$this,'ajax_user_posts') );
		add_action( 'wp_ajax_nopriv_peepsoblogposts_user_posts', array(&$this,'ajax_user_posts') );

		add_action( 'wp_ajax_peepsoblogposts_css', array(&$this,'ajax_css') );
		add_action( 'wp_ajax_nopriv_peepsoblogposts_css', array(&$this,'ajax_css') );

		register_activation_hook(__FILE__, array(&$this, 'activate'));
	}

	public function load_textdomain()
	{
		$path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
		load_plugin_textdomain('peepsoblogposts', FALSE, $path);
	}

	// @todo might do nothing
	private function _init_post_types()
	{
		// remove spaces and attach ",post" for explode()
		if(NULL == $this->post_types && class_exists('PeepSo')) {
			$post_types = str_replace(' ', '', PeepSo::get_option('blogposts_activity_post_types', 'post'));
			$post_types .= ',*';
			$this->post_types=explode(',',$post_types);
		}
	}

	/**
	 * get the singleton instance
	 *
	 * @return PeepSoBlogPosts
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}

	/**
	 * register hooks
	 *
	 * @return void
	 */
	public function init()
	{
		// Register classes and templates with PeepSo Core
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);

		if(1==PeepSo::get_option('blogposts_profile_template_overrides', 0)) {
			PeepSoTemplate::add_template_directory(PeepSo::get_peepso_dir() . 'peepso-blogposts');
		}
		
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'check_peepso'));

			// Add PeepSo Config tab
			add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
		} else {
			// Enqueue frontend JS & CSS
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

			// Activity Stream item - User X "wrote a Z" text
			add_filter('peepso_activity_stream_action', array(&$this, 'activity_stream_action'), 10, 2);

			// Activity Item parser - include the embed
			add_filter('the_content', array(&$this, 'the_content'),1,2);

			// "Blog Posts" profile section
			if(PeepSo::get_option('blogposts_profile_enable', 0)) {


				// "User Profile" PeepSo Core widget
				add_filter('peepso_widget_me_links', array(&$this, 'peepso_widget_me_links'));

				// Profile segment menu item
				add_filter('peepso_profile_segment_menu_links', array(&$this, 'peepso_profile_segment_menu_links'));

				// Profile segment renderer
				add_action('peepso_profile_segment_blogposts', array(&$this, 'peepso_profile_segment_blogposts'));

				// Profile segment page title
				add_filter('peepso_page_title_profile_segment', array(&$this, 'peepso_page_title_profile_segment'));

				// @todo what is this
				add_filter('peepso_rewrite_profile_pages', array(&$this, 'peepso_rewrite_profile_pages'));
			}
		}



		// Register actions for each post type
		$post_types = PeepSoBlogPosts::get_post_types();

		foreach($post_types as $post_type) {
			add_action( 'publish_'.$post_type, array(&$this, 'publish_post'), 1, 2 );
		}
	}

	/* * * PROFILE SEGMENT * * */

	/**
	 * create a menu item in the PeepSo Profile widget menu
	 * @param $links
	 * @return mixed
	 */
	public function peepso_widget_me_links($links)
	{
		// @todo ordering
		$user = new PeepSoUser(PeepSo::get_user_id());
		$links[50][] = array(
			'href' => $user->get_profileurl().'blogposts',
			'title' => __('Blog Posts', 'peepsoblogposts'),
			'icon' => 'ps-icon-pencil',
		);

		ksort($links);
		return $links;
	}

	/**
	 * create a menu item in the PeepSo profile segments menu
	 *
	 * @param $links
	 * @return mixed
	 */
	public function peepso_profile_segment_menu_links($links)
	{
		// @todo ordering
		$links[50][] = array(
			'href' => 'blogposts',
			'title'=> __('Blog Posts', 'peepsoblogposts'),
			'id' => 'blogposts',
			'icon' => 'pencil'
		);

		ksort($links);
		return $links;
	}

	/**
	 * adjust the profile segment page title
	 *
	 * @param $title
	 * @return mixed
	 */
	public function peepso_page_title_profile_segment( $title )
	{
		if( 'blogposts' === $title['profile_segment']) {
			$title['newtitle'] = $title['title'] . " - ". __('Blog Posts', 'peepsoblogposts');
		}

		return $title;
	}

	/**
	 * render the Blogposts profile segment
	 *
	 * @return void
	 */
	public function peepso_profile_segment_blogposts()
	{
		// Get the currently viewed User ID from PeepSoProfileShortcode and exec template
		$pro = PeepSoProfileShortcode::get_instance();
		$this->view_user_id = PeepSoUrlSegments::get_view_id($pro->get_view_user_id());

		echo PeepSoTemplate::exec_template('blogposts', 'blogposts', array('view_user_id' => $this->view_user_id), TRUE);
	}

	/**
	 * @todo not sure what this does
	 *
	 * @param $pages
	 * @return array
	 */
	public function peepso_rewrite_profile_pages($pages)
	{
		return array_merge($pages, array('posts'));
	}

	public function ajax_css()
	{
		ob_start();
		header('Content-type: text/css');
		?>
		.ps-blogposts-featured-image {
			height: <?php echo intval(PeepSo::get_option('blogposts_profile_featured_image_height', 150));?>px;
		<?php
		if('top' != PeepSo::get_option('blogposts_profile_featured_image_position','top')) { ?>
			width: <?php echo intval(PeepSo::get_option('blogposts_profile_featured_image_height', 150));?>px;
		<?php } ?>
			background-position: <?php echo PeepSo::get_option('blogposts_profile_featured_image_align','center');?> <?php echo PeepSo::get_option('blogposts_profile_featured_image_align_vertical','center');?> !important;
		}

		.ps-blogposts-container-half {
			height: <?php echo intval(PeepSo::get_option('blogposts_profile_two_column_height', 350));?>px;
			<?php if(PeepSo::get_option('blogposts_profile_two_column_enable_overflow_hide', 1)) :?>
			overflow:hidden;
			<?php endif;?>
		}

		<?php
		ob_end_flush();
		exit(0);
	}


	/**
	 * Build AJAX response with user blog posts
	 */
	public function ajax_user_posts()
	{
		ob_start();

		$input = new PeepSoInput();
		$owner = $input->post_int('user_id');
		$page  = $input->post_int('page', 1);

		$sort  = $input->post('sort', 'desc');

		$blogposts_per_page = intval(PeepSo::get_option('site_activity_posts', 10));
		$offset = ($page - 1) * $blogposts_per_page;

		if ($page < 1) {
			$page = 1;
			$offset = 0;
		}

		$args = array(
			'author'        => $owner,
			'orderby'       => 'post_date',
			'post_status'	=> 'publish',
			'order'         => $sort,
			'posts_per_page'=> $blogposts_per_page,
			'offset'		=> $offset,
		);

		// Count published posts
		$count_posts = wp_count_posts();
		$count_blogposts = $count_posts->publish;

		// Get the posts
		$blogposts=get_posts($args);


		if (count($blogposts)) {
			// Iterate posts
			foreach ($blogposts as $post) {

				// Choose between excerpt or post_content
				// @todo is there a more elegant way?
				$post_content = get_the_excerpt($post->ID);

				if(!strlen($post_content)) {
					$post_content = $post->post_content;
				}

				$limit = intval(PeepSo::get_option('blogposts_profile_content_length',50));
				$post_content = wp_trim_words($post_content, $limit,'&hellip;');

				if(0 == $limit) {
					$post_content = FALSE;
				}

				// date positon
				$date_position = PeepSo::get_option('blogposts_profile_date_position','top');

				// image position
				$image_position = PeepSo::get_option('blogposts_profile_featured_image_position','top');

				// Featured image
				// @todo make this a template file?

				if(PeepSo::get_option('blogposts_profile_featured_image_enable') &&
					(has_post_thumbnail($post) || PeepSo::get_option('blogposts_profile_featured_image_enable_if_empty'))) {
					ob_start();
					?>

					<div style="background: url('<?php echo get_the_post_thumbnail_url($post);?>');" class="ps-blogposts-featured-image ps-blogposts-featured-image-<?php echo $image_position;?>"></div>

					<?php
					$image = ob_get_clean();
				}

				$args = compact('post_content', 'date_position', 'image_position', 'image', 'post');

				if(PeepSo::get_option('blogposts_profile_two_column_enable',0)) {
					PeepSoTemplate::exec_template('blogposts','blogpost_half', $args);
				} else {
					PeepSoTemplate::exec_template('blogposts', 'blogpost_wide', $args);
				}
			}
		} else {
			// @todo message when nothing found
		}

		$resp['success']		= 1;
		$resp['page']			= $page;
		$resp['found_blogposts']= abs($count_blogposts - $page * $blogposts_per_page);
		$resp['html']			= ob_get_clean();

		header('Content-Type: application/json');
		echo json_encode($resp);
		exit(0);
	}

	/* * *  UTILITIES * * */

	/**
	 * enqueue JS  & CSS
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{

		wp_enqueue_style('peepso-blogposts', plugin_dir_url(__FILE__) . 'assets/css/blogposts.css', array(), self::PLUGIN_VERSION, 'all');
		wp_enqueue_style('peepso-blogposts-dynamic', admin_url('admin-ajax.php') . '?action=peepsoblogposts_css', array(), self::PLUGIN_VERSION, 'all');

		// Main JS file
		wp_enqueue_script('peepso-blogposts',
			plugin_dir_url(__FILE__) . 'assets/js/blogposts.min.js',
			array('peepso'), self::PLUGIN_VERSION,
			TRUE
		);

		// Pass variables necessary to build the AJAX call
		wp_localize_script( 'peepso-blogposts', 'peepso_blogposts_data',
			array( 'url' => admin_url( 'admin-ajax.php' ), 'action' => 'peepsoblogposts_user_posts') );

	}

	/**
	 * return all post types without the blacklisted ones
	 *
	 * @return array
	 */
	public static function get_post_types()
	{
		return array_diff(get_post_types(), self::$post_types_blacklist);
	}

	/* * * ACTIVITY STREAM * * */

	/**
	 * create an Activity Stream item when a new post is published
	 *
	 * @param int 		$ID
	 * @param WP_Post 	$post
	 * @return bool
	 */
	function publish_post( $ID, $post ) {

		// @todo might do nothing
		$this->_init_post_types();

		// make sure the post type is enabled
		if(!PeepSo::get_option('blogposts_activity_type_'.$post->post_type, 0)) {
			return FALSE;
		}

		// double check in case a post type is phased out but still remains configured
		if(in_array($post->post_type,  self::$post_types_blacklist)) {
			return FALSE;
		}

		// author is not always the current user - ie when admin publishes a post written by someone else
		$author_id = $post->post_author;

		// #1 user notifications
		// @todo

		// #2 activity item
		if($this->_check_activity($ID, $author_id)) {

			$act = PeepSoActivity::get_instance();

			$extra = array(
					'module_id' => self::MODULE_ID,
					'act_access'=> PeepSo::get_option('blogposts_ativity_privacy',PeepSo::get_user_access($author_id)),
			);

			// mark this post as already posted to activity
			add_post_meta($ID, self::SHORTCODE, TRUE, TRUE);

			// build JSON to be used as post content for later display
			$content = array(
				'post_id' => $ID,
				'post_type' => $post->post_type,
				'shortcode' => self::SHORTCODE,
				'permalink' => get_permalink($ID),
			);

			$content=json_encode($content);

			$act->add_post($author_id, $author_id, $content, $extra);
		}
	}

	/**
	 * check if an activity stream should be created for a given post ID
	 *
	 * @param $ID
	 * @param $author_id
	 * @return bool
	 */
	private function _check_activity($ID, $author_id)
	{
		// check if it's not marked as already posted to activity
		if (strlen(get_post_meta($ID, self::SHORTCODE, TRUE))) {
			return( FALSE );
		}

		// check if activity posting is enabled
		if (0 == PeepSo::get_option('blogposts_activity_enable', 0 )) {
			return( FALSE );
		}

		return( TRUE );
	}

	/**
	 * define the "action text" depending on post type eg "published a page"
	 *
	 * @param $action
	 * @param $post
	 * @return string
	 */
	public function activity_stream_action($action, $post)
	{
		if (self::MODULE_ID === intval($post->act_module_id)) {

			$action = PeepSo::get_option('blogposts_activity_type_post_text_default');

			// @since 0.0.5
			$content = strip_tags(get_post_field('post_content', $post, 'raw'));
			if($target_post = json_decode($content)) {
				$key_text = 'blogposts_activity_type_'.$target_post->post_type.'_text';
				$action = PeepSo::get_option($key_text, $action);
			}
		}

		return ($action);
	}

	/**
	 * parse the activity item JSON to force a nice embed
	 *
	 * @param $content
	 * @param null $post
	 * @return string
	 */
	public function the_content( $content, $post = NULL )
	{
		if(stristr($content, self::SHORTCODE)) {
			// @since 0.0.5
			if($target_post = json_decode($content)) {
				$content = $target_post->permalink;
			} else {
				$content = '<div style=display:none>' . $content . '</div>';
			}
		}
		return $content;
	}

	/* * * ADMINISTRATION * * */

	public function activate()
	{
		if (!$this->check_peepso()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoBlogPostsInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}

		return (TRUE);
	}

	public function check_peepso()
	{
		if (!class_exists('PeepSo'))
		{
			if (is_plugin_active(plugin_basename(__FILE__))) {
				// deactivate the plugin
				deactivate_plugins(plugin_basename(__FILE__));
				// display notice for admin
				add_action('admin_notices', array(&$this, 'disabled_notice'));
				if (isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
			}
			return (FALSE);
		}

		return (TRUE);
	}

	public function disabled_notice()
	{
		echo '<div class="error fade">';
		echo
		'<strong>' , self::PLUGIN_NAME , ' ' ,
		__('plugin requires the PeepSo plugin to be installed and activated.', 'peepso'),
		'</a>',
		'</strong>';
		echo '</div>';
	}

	public function admin_config_tabs( $tabs )
	{
		$tabs['blogposts'] = array(
				'label' => __('Blog Posts', 'peepsoblogposts'),
				'tab' => 'blogposts',
				'description' => __('Example Config Tab', 'peepsoblogposts'),
				'function' => 'PeepSoConfigSectionBlogPosts',
		);

		return $tabs;
	}
}

PeepSoBlogPosts::get_instance();

// EOF