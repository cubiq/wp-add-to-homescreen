<?php

class Cubiq_Add_To_Home_Admin {

	private static $_instance;

	private $_slug;
	private $_version;
	private $_options;

	public function __construct () {
		$plugin_class = str_replace( array('_Admin', '_Public'), '', get_class() );
		$this->_slug = strtolower( str_replace('_', '-', $plugin_class) );
		$this->_version = $plugin_class::VERSION;

		add_action('admin_init', array( &$this, 'admin_init' ));

		add_action('admin_menu', array( &$this, 'add_menu' ));
		add_action('admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ));

		add_filter('plugin_action_links_' . plugin_basename( realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' ) ) . DIRECTORY_SEPARATOR . $this->_slug . '.php', array( &$this, 'plugin_action_links' ));
	}

	public static function get_instance () {
		if ( !isset(self::$_instance) ) {
			self::$_instance = new self ();
		}

		return self::$_instance;
	}

	public function plugin_action_links ($links) {
		array_unshift($links, '<a href="options-general.php?page=' . $this->_slug . '">' . __('Settings') . '</a>');
		return $links;
	}

	public function enqueue_scripts ($hook) {
		if ( $hook !== 'settings_page_' . $this->_slug ) {
			return;
		}

		// load the media library
		wp_enqueue_media();

		// load custom scripts
		$basepath = plugins_url( 'assets' , dirname(__FILE__) ) . DIRECTORY_SEPARATOR;
		wp_enqueue_script( $this->_slug . '-admin-script', $basepath . 'admin.js', array('jquery'), $this->_version );
		wp_enqueue_script( $this->_slug . '-flot', $basepath . 'jquery.flot.min.js', null, $this->_version );
		wp_enqueue_script( $this->_slug . '-flot-categories', $basepath . 'jquery.flot.categories.min.js', null, $this->_version );
		wp_enqueue_script( $this->_slug . '-flot-resize', $basepath . 'jquery.flot.resize.min.js', null, $this->_version );
		wp_enqueue_script( $this->_slug . '-add-to-homescreen', $basepath . 'addtohomescreen.min.js', null, $this->_version );

		// load the dash icons
		wp_enqueue_style('dashicons');

		// needed by media library
		//wp_register_style('editor-buttons-classic', includes_url() . 'css/editor.min.css');

		//wp_enqueue_style('editor-buttons-classic');
	}

	public function admin_init () {
		// load options for later use
		$this->_options = get_option($this->_slug);

		// we use aggregated settings for cleanness sake
		register_setting($this->_slug . '-group', $this->_slug, array( &$this, 'sanitize' ));

		/*
		 * Main sections
		 */
		add_settings_section(
			$this->_slug . '-advanced',
			__('Advanced Options', $this->_slug),
			array(&$this, 'dev_null'),
			$this->_slug
		);

		add_settings_section(
			$this->_slug . '-detect',
			__('Detect Homescreen Mechanism', $this->_slug),
			array(&$this, 'dev_null'),
			$this->_slug
		);

		add_settings_section(
			$this->_slug . '-debug',
			__('Debug &amp; Development', $this->_slug),
			array(&$this, 'dev_null'),
			$this->_slug
		);

		add_settings_section(
			$this->_slug . '-stats',
			__('Statistics', $this->_slug),
			array(&$this, 'dev_null'),
			$this->_slug
		);

		/*
		 * Fields
		 */
		add_settings_field(
			$this->_slug . '-title',
			__('Homescreen Title', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'title',
				'default' => '',
				'desc' => __('The title that will be displayed in the homescreen. Leave blank to use the page title. Note: Android does not support this feature.')
			)
		);

		add_settings_field(
			$this->_slug . '-active-page',
			__('Active Page', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'active_page',
				'default' => '',
				'desc' => __('Slug, title, ID or full URL of the page where the message should be shown on. Leave blank for the front page.')
			)
		);

		add_settings_field(
			$this->_slug . '-skip-first-visit',
			__('Skip first visit', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'skip_first_visit',
				'default' => 1,
				'desc' => __('If checked the callout is shown to returning visitors only. Don\'t annoy first time visitors, they probably just want to look around. Default: checked')
			)
		);

		add_settings_field(
			$this->_slug . '-display-pace',
			__('Display pace', $this->_slug),
			array(&$this, 'input_select'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'display_pace',
				'default' => 1440,
				'options' => array(
					15     => __('every 15 minutes'),
					30     => __('every 30 minutes'),
					60     => __('every hour'),
					120    => __('every 2 hours'),
					180    => __('every 3 hours'),
					360    => __('every 12 hours'),
					1440   => __('every 24 hours'),
					2880   => __('every other day'),
					10080  => __('every 7 days'),
					21600  => __('every 15 days'),
					43200  => __('every 30 days'),
					86400  => __('every 2 months'),
					129600 => __('every 3 months'),
					0      => __('Everytime')
				),
				'desc' => __('Time interval before showing the message again. Don\'t nag your users too much, one callout per day is more than enough. Default: every 24 hours')
			)
		);

		add_settings_field(
			$this->_slug . '-max-display-count',
			__('Max display count', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'max_display_count',
				'default' => 1,
				'desc' => __('The absolute maximum number of times the message is shown to any given user. It\'s good practice to show the callout just one time. Default: 1 (0 = no limit)')
			)
		);

		add_settings_field(
			$this->_slug . '-start-delay',
			__('Start delay', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'start_delay',
				'default' => 1,
				'desc' => __('Seconds to wait from page load before the message is shown. Default: 1')
			)
		);

		add_settings_field(
			$this->_slug . '-lifespan',
			__('Lifespan', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'lifespan',
				'default' => 20,
				'desc' => __('Seconds before the callout is automatically hidden. Default: 20')
			)
		);

		add_settings_field(
			$this->_slug . '-message',
			__('Custom message', $this->_slug),
			array(&$this, 'input_textarea'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'message',
				'default' => '',
				'desc' => __('Change the default message with one of your own. Leave blank to use the default.')
			)
		);

		add_settings_field(
			$this->_slug . '-display_icon',
			__('Display icon', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'display_icon',
				'default' => 1,
				'desc' => __('Include the application icon in the callout. Default: checked')
			)
		);

		add_settings_field(
			$this->_slug . '-meta_tags',
			__('Add meta tags', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'meta_tags',
				'default' => 1,
				'desc' => __('Automatically adds the needed meta tags in the page head. If disabled they have to be added manually.')
			)
		);

		add_settings_field(
			$this->_slug . '-modal',
			__('Modal', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'modal',
				'desc' => __('When checked the website content is blurred until the user closes the callout. Default: disabled')
			)
		);

		add_settings_field(
			$this->_slug . '-silent',
			__('Silent mode', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-advanced',
			array(
				'field' => 'silent',
				'desc' => __('This effectively prevents the message to pop up but users are still tracked. It is useful if you want the stats without the callout. Default: disabled')
			)
		);

		add_settings_field(
			$this->_slug . '-display-pace',
			__('Detect homescreen mechanism', $this->_slug),
			array(&$this, 'input_fieldset'),
			$this->_slug,
			$this->_slug . '-detect',
			array(
				'field' => 'detect_homescreen',
				'default' => 'detect-no-tracking',
				'options' => array(
					'detect-no-tracking'	=> array(__('No tracking'), __("This is the safest choice, but tracking is disabled. It is useful when all the other options can't be implemented.")),
					'detect-webapp-capable'	=> array(__('The web site is web-app-capable'), __('Note that your web site has to be specifically crafted for this mode.')),
					'detect-hash'			=> array(__('Location Hash'), __('The script adds a special token to the location hash, eg: www.example.com/#ath')),
					'detect-query-string'	=> array(__('Query string'), __('The script adds a special token to the query string, eg: www.example.com/?ath=')),
					'detect-smart-url'		=> array(__('Smart URL'), __('The script adds a special token to the smart URL. This is an advanced feature, do not use unless you know what you are doing! eg: www.example.com/ath/'))
				)
			)
		);

		add_settings_field(
			$this->_slug . '-debug',
			__('Enable debug mode', $this->_slug),
			array(&$this, 'input_checkbox'),
			$this->_slug,
			$this->_slug . '-debug',
			array(
				'field' => 'debug',
				'desc' => __('Show the callout to desktop browser and unsopported devices. Useful in development phase.')
			)
		);

		add_settings_field(
			$this->_slug . '-debug-emulation',
			__('Emulated device', $this->_slug),
			array(&$this, 'input_select'),
			$this->_slug,
			$this->_slug . '-debug',
			array(
				'field' => 'debug_emulation',
				'default' => 'auto',
				'options' => array(
					'auto'    => __('auto'),
					'ios'     => __('iOS'),
					'android' => __('Android')
				),
				'desc' => __('This forces the device detection to a specific OS, mainly for presentational purpose.')
			)
		);

		add_settings_field(
			$this->_slug . '-application-id',
			__('Application ID', $this->_slug),
			array(&$this, 'input_text'),
			$this->_slug,
			$this->_slug . '-debug',
			array(
				'field' => 'application_id',
				'default' => 'org.cubiq.addtohome',
				'desc' => __('This is only needed if you have more than one application per domain, in which case you may want to give each site a different application ID. <strong>It should be changed only one time!</strong> Changing this will make the callout popping up to all users all over again.')
			)
		);

		add_settings_field(
			$this->_slug . '-stats-type',
			__('Stats type', $this->_slug),
			array(&$this, 'input_fieldset'),
			$this->_slug,
			$this->_slug . '-stats',
			array(
				'field' => 'stats_type',
				'default' => 'stats-internal',
				'options' => array(
					'stats-internal'	=> array(__('Integrated Statistics'), __("Use a local mysql database to store statistics.")),
					'stats-ga'		=> array(__('Google Analytics (beta)'), __('Integrate with Google Analytics (beta: please report any problem). NOTE: GA must be already active on your wesite!'))
				)
			)
		);
	}

	public function add_menu () {
		add_options_page(
			__('Add To Homescreen Settings', $this->_slug),
			__('Add To Homescreen', $this->_slug),
			'manage_options',
			$this->_slug,
			array( &$this, 'load_template' )
		);
	}

	private function dev_null () {

	}

	public function input_checkbox ($args) {
		$field = $args['field'];
		$value = $this->_options;
		$value = isset( $value[$field] ) ? $value[$field] : $args['default'];

		$checked = checked(1, $value, false);

		echo sprintf('<input type="checkbox" name="%s[%s]" id="%s" value="1" %s />', $this->_slug, $field, $field, $checked);
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	public function input_text ($args) {
		$field = $args['field'];
		$value = $this->_options;
		$value = esc_attr(( isset($value[$field]) ? $value[$field] : $args['default'] ));
		if ( !$value && $value !== '' ) {
			$value = '0';
		}

		echo sprintf('<input type="text" name="%s[%s]" id="%s" value="%s" />', $this->_slug, $field, $field, $value);
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	public function input_select ($args) {
		$field = $args['field'];
		$value = $this->_options;
		$value = isset($value[$field]) ? $value[$field] : $args['default'];

		echo sprintf('<select name="%s[%s]" id="%s">', $this->_slug, $field, $field);

		foreach ($args['options'] as $option => $label) {
			$selected = $option == $value ? ' selected' : '';
			echo sprintf('<option value="%s"%s>%s</option>'."\n", $option, $selected, $label);
		}

		echo "</select>\n";
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	public function input_textarea ($args) {
		$field = $args['field'];
		$value = $this->_options;
		$value = esc_attr(( isset($value[$field]) ? $value[$field] : $args['default'] ));

		echo sprintf('<textarea name="%s[%s]" id="%s">%s</textarea>', $this->_slug, $field, $field, $value);
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	public function input_fieldset ($args) {
		$field = $args['field'];
		$selected = $this->_options;
		$selected = isset($selected[$field]) ? $selected[$field] : $args['default'];

		echo '<fieldset>';
		foreach ($args['options'] as $value => $label) {
			$checked = $value == $selected ? 'checked ' : '';
			echo sprintf('<label for="%s"><input type="radio" name="%s[%s]" id="%s" value="%s" %s/>' . "\n" . '<span><strong>%s</strong></span><p class="description">%s</p></label><br />'."\n",
				$value, $this->_slug, $field, $value, $value, $checked, $label[0], $label[1]);
		}
		echo '</fieldset>';
	}

	public function sanitize ($post) {
		// options defaults
		$defaults = array(
			'skip_first_visit' 	=> 0,
			'display_icon'		=> 0,
			'meta_tags'			=> 0,
			'modal'				=> 0,
			'silent'			=> 0,
			'debug'				=> 0
		);
		$post = array_merge($defaults, $post);

		// application ID
		$post['application_id'] = preg_replace("/[^A-Za-z0-9\.\-_]/", '', $post['application_id']);
		if ( empty($post['application_id']) ) {
			$post['application_id'] = 'org.cubiq.addtohome';
		}

		// create the icons
		$sizes = array(
			'196x196' => array('width' => 196, 'height' => 196),
			'152x152' => array('width' => 152, 'height' => 152),
			'144x144' => array('width' => 144, 'height' => 144),
			'120x120' => array('width' => 120, 'height' => 120),
			'114x114' => array('width' => 114, 'height' => 114),
			'76x76'   => array('width' =>  76, 'height' =>  76),
			'72x72'   => array('width' =>  72, 'height' =>  72)
		);

		// Icons path
		$basepath = wp_upload_dir();
		$basepath = $basepath['basedir'] . DIRECTORY_SEPARATOR . $this->_slug;

		$newIconID = intval( $post['_new_application_icon'] );
		$oldIconID = intval( $post['application_icon'] );
		unset( $post['_new_application_icon'] );

		// delete icon files and exit
		if ( empty($newIconID) && empty($oldIconID) && !empty($this->_options['application_icon']) ) {
			foreach ($sizes as $key => $value) {
				@unlink( $basepath . DIRECTORY_SEPARATOR . $this->_options['application_icon'] . '-application-icon-' . $key . '.png' );
			}

			$post['application_icon'] = '';
			return $post;
		}

		// no new icon to upload
		if ( empty($newIconID) ) {
			return $post;
		}

		// try to create the plugin directory under the /uploads folder
		if ( !is_dir($basepath) && !mkdir($basepath, 0755, true) ) {
			$post['application_icon'] = '';
			return $post;
		}

		$post['application_icon'] = $newIconID;

		$filename = get_attached_file( $newIconID );
		$img = wp_get_image_editor( $filename );

		// resize the icon in all needed formats
		foreach ($sizes as $key => $value) {
			// delete old icon
			if ( $oldIconID ) {
				@unlink( $basepath . DIRECTORY_SEPARATOR . 'application-icon-' . $oldIconID . '-' . $key . '.png' );
			}

			$img->resize($value['width'], $value['height'], true);
			$img->save( $basepath . DIRECTORY_SEPARATOR . 'application-icon-' . $newIconID . '-' . $key . '.png', 'image/png' );
		}

		return $post;
	}

	public function load_template () {
		global $wpdb;

		$opt = $this->_options;

		// load stats
		$stats = $wpdb->get_results( "SELECT `time`, `device`, `action` FROM `" . $wpdb->prefix . strtolower( str_replace( array('_Admin', '_Public'), '', get_class() )) . "` WHERE `time` > " . ( time() - 60*60*24*30 ) . " ORDER BY `id` ASC");

		$displayData = array();
		$addData = array();

		// set a 30 days period
		$startDate = strtotime("-29 days");

		for ( $i = 0; $i < 30; $i++ ) {
			$currentDate = date('j M', strtotime("+ " . $i . " days", $startDate));
			$displayData[$currentDate] = 0;
			$addData[$currentDate] = 0;
		}

		// fill the timeline with our data
		foreach ($stats as $key => $value) {
			if ( $value->action == 1 ) {
				$displayData[date('j M', $value->time)]++;
			} else {
				$addData[date('j M', $value->time)]++;
			}
		}

		// load template
		include ( realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' ) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin.php' );
	}

	public static function message_stats () {
		global $wpdb;

		$OS = !empty( $_POST['o'] ) ? $_POST['o'] : false;
		$action = !empty( $_POST['a'] ) ? (int)$_POST['a'] : false;
		$device = !empty( $_POST['d'] ) ? 'tablet' : 'phone';

		if ( ($OS == 'android' || $OS == 'ios' || $OS == 'windows') && ($action == 1 || $action == 2) ) {
			$wpdb->insert(
				$wpdb->prefix . strtolower( str_replace( array('_Admin', '_Public'), '', get_class() )),
				array(
					'time' => time(),
					'device' => $OS . ':' . $device,
					'action' => $action
				),
				array(
					'%d',
					'%s',
					'%d'
				)
			);
		}

		die();
	}
}
