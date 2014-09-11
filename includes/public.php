<?php

class Cubiq_Add_To_Home_Public {

	private static $_instance;

	private $_slug;
	private $_version;
	private $_options;
	private $_isCompatible = false;

	public function __construct () {
		$plugin_class = str_replace( array('_Admin', '_Public'), '', get_class() );
		$this->_slug = strtolower( str_replace('_', '-', $plugin_class) );
		$this->_version = $plugin_class::VERSION;
		$this->_options = get_option($this->_slug);

		$this->normalize();

		if ( $this->_isCompatible ) {
			add_action('wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ));
		}

		add_action( 'wp_head', array( &$this, 'head' ) );
	}

	public static function get_instance () {
		if ( !isset(self::$_instance) ) {
			self::$_instance = new self ($slug);
		}

		return self::$_instance;
	}

	public function normalize () {
		$opt = $this->_options;

		// normalize options for javascript
		$opt['silent'] = isset( $opt['silent'] ) && $opt['silent'] == 1 ? true : false;
		$opt['skip_first_visit'] = !isset( $opt['skip_first_visit'] ) ? 'true' : $opt['skip_first_visit'] == 1 ? 'true' : 'false';
		$opt['display_pace'] = isset( $opt['display_pace'] ) ? (int)$opt['display_pace'] : 1440;
		$opt['max_display_count'] = isset( $opt['max_display_count'] ) ? (int)$opt['max_display_count'] : 1;
		$opt['start_delay'] = isset( $opt['start_delay'] ) ? (int)$opt['start_delay'] : 1;
		$opt['lifespan'] = isset( $opt['lifespan'] ) ? (int)$opt['lifespan'] : 20;
		$opt['modal'] = isset( $opt['modal'] ) && $opt['modal'] == 1 ? 'true' : 'false';
		$opt['debug'] = isset( $opt['debug'] ) && $opt['debug'] == 1 ? 'true' : 'false';
		$opt['display_icon'] = !isset( $opt['display_icon'] ) ? 'true' : $opt['display_icon'] == 1 ? 'true' : 'false';
		$opt['meta_tags'] = !isset( $opt['meta_tags'] ) ? 'true' : $opt['meta_tags'] == 1 ? 'true' : 'false';
		$opt['message'] = !isset( $opt['message'] ) ? '' : esc_html( $opt['message'] );
		$opt['title'] = !isset( $opt['title'] ) ? '' : esc_attr( $opt['title'] );
		$opt['stats_type'] = !isset( $opt['stats_type'] ) ? 'stats-internal' : esc_attr($opt['stats_type']);
		$opt['active_page'] = empty( $opt['active_page'] ) ? '' : $opt['active_page'];

		if ( !empty($opt['debug_emulation']) && ( $opt['debug_emulation'] == 'ios' || $opt['debug_emulation'] == 'android' ) ) {
			$opt['debug'] = "'" . $opt['debug_emulation'] . "'";
		}

		$opt['application_id'] = !empty( $opt['application_id'] ) ? $opt['application_id'] : 'org.cubiq.addtohome';

		switch ( $opt['detect_homescreen'] ) {
			case 'detect-hash':
				$opt['detect_homescreen'] = "'hash'";
				break;
			case 'detect-query-string':
				$opt['detect_homescreen'] = "'queryString'";
				break;
			case 'detect-smart-url':
				$opt['detect_homescreen'] = "'smartURL'";
				break;
			case 'detect-webapp-capable':
				$opt['detect_homescreen'] = 'false';
				$opt['webapp_capable'] = true;
				break;
			default:
				$opt['detect_homescreen'] = 'false';
		}

		$ua = $_SERVER['HTTP_USER_AGENT'];
		$isMobileSafari = preg_match("/iPhone|iPod|iPad/", $ua) && strpos($ua, 'Safari') !== false && strpos($ua, 'CriOS') === false;
		$isMobileChrome = preg_match("/Chrome\/[.0-9]*/", $ua) && strpos($ua, 'Android') !== false;

		$this->_isCompatible = ( $isMobileSafari || $isMobileChrome ) || !empty( $this->_options['debug'] );

		// expose the normalized options
		$this->_options = $opt;
	}

	public function head () {
		global $wp;

		$opt = $this->_options;
		$isCompatible = $this->_isCompatible;
		$ajaxURL = admin_url('admin-ajax.php');

		// check if the page is the one we want the message on
		if (
			( empty($opt['active_page']) && !is_front_page() ) ||
			( !empty($opt['active_page']) && ( !is_page($opt['active_page']) && home_url( $wp->request ) != $opt['active_page'] ) )
		) {
			return;
		}

		// if the homescreen detection mechanism is enabled we load some logic anyway to remove the token from URL
		if ( !$isCompatible && $opt['detect_homescreen'] == 'false' ) {
			return;
		}

		include ( realpath(dirname(__FILE__) . '/..' ) . '/templates/public.php' );
	}

	public function enqueue_scripts () {
		wp_enqueue_script('jquery');

		$basepath = plugins_url( 'assets' , dirname(__FILE__) ) . DIRECTORY_SEPARATOR;
		wp_enqueue_script( $this->_slug . '-main', $basepath . 'addtohomescreen.min.js', array(), $this->_version );
		wp_enqueue_style( $this->_slug . '-style', $basepath . 'addtohomescreen.css', array(), $this->_version );
	}

}
