<?php
defined( 'ABSPATH' ) or die('Do not directly call this script');
/**
 * Plugin Name: Easy Development Mode
 * Plugin URI: https://github.com/DeusMachineLLC/easy-development-mode
 * Author: Lee Ralls, Deus Machine LLC
 * Author URI: http://deusmachine.com
 * Description: Restrict access on your globally-accessible development server to a single (or multiple) IP address, with the option to redirect somewhere else.
 * Version: 1.0
 */

class Easy_Development_Mode {

	public $ip_array;
	public $user;
	public $settings_name;
	private $options;
	private $admin;

	/**
	 * Setup
	 */
	public function __construct() {

		# set values
		$this->user = $_SERVER['REMOTE_ADDR'];
		$this->settings_group = 'devmode_settings';
		$this->settings_name = $this->settings_group;
		$this->admin = false;

		# setup menu
		register_activation_hook(__FILE__, array( $this, 'activate' ) );
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'init', array( $this, 'restrict_ips' ) );
	}

	public function activate() {
		$ez_devmode_settings = array(
			'ez_redirect_url' => 'http://google.com',
			'ez_allowed_ips' => $this->user
		);
		update_option( $this->settings_group, $ez_devmode_settings );
	}

	/**
	 * Settings, derived from Ex#2 on Wordpress Codex
	 */
	public function page_init() {
		register_setting(
			$this->settings_group,
			$this->settings_name,
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'ez_devmode_settings',
			'Easy Development Mode Settings',
			array( $this, 'print_section_info' ),
			__FILE__
		);

		add_settings_field(
			'ez_redirect_url',
			'Redirect URL',
			array( $this, 'redir_url_callback' ),
			__FILE__,
			'ez_devmode_settings'
		);

		add_settings_field(
			'allowed_ips',
			'Allowed IP Addresses',
			array( $this, 'allowed_ips_callback' ),
			__FILE__,
			'ez_devmode_settings'
		);
	}

	/**
	 * Sanitize
	 * @param $input
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['ez_redirect_url'] ) ) {
			$new_input['ez_redirect_url'] = sanitize_text_field($input['ez_redirect_url']);
		}

		if( isset( $input['ez_allowed_ips'] ) ) {
			$new_input['ez_allowed_ips'] = sanitize_text_field($input['ez_allowed_ips'] );
		}

		return $new_input;
	}

	/**
	 * Title
	 */
	public function print_section_info() {
		print 'Enter your settings here';
	}

	/**
	 * Callback for redirect url option
	 */
	public function redir_url_callback() {
		printf(
			'<input type="text" id="redirect_url" name="devmode_settings[ez_redirect_url]" size="25" value="%s" />',
			isset( $this->options['ez_redirect_url'] ) ? esc_attr( $this->options['ez_redirect_url'] ) : ''
		);
	}

	/**
	 * Callback for allowed IPs
	 */
	public function allowed_ips_callback() {
		?>
		<p>Your IP Address: <strong><?php echo $this->user;?></strong></p><br />
		<?php
		printf(
			'<input type="text" id="allowed_ips" name="devmode_settings[ez_allowed_ips]" size="25" value="%s">',
			isset( $this->options['ez_allowed_ips'] ) ? esc_attr( $this->options['ez_allowed_ips'] ) : ''
		);
		?>
		<p>

				Comma-separate each IP address when using multiple.<br />
			<em>
				Example: 127.0.0.1, 192.168.1.2, etc.
			</em>
		</p>
		<?php
	}

	/**
	 * Create the admin menu
	 */
	public function create_admin_menu() {
		add_options_page(
			'Development Mode Settings',
			'Development Mode Settings',
			'administrator',
			__FILE__,
			array( $this, 'development_mode_settings_page' )
		);
	}

	/**
	 * Page actual
	 */
	public function development_mode_settings_page() {
		$this->options = get_option( $this->settings_group );
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->settings_group);
				do_settings_sections( __FILE__ );
				submit_button();
				?>
			</form>
		</div>

		<?php

	}

	/**
	 * Maybe restrict IPs
	 */
	public function restrict_ips() {
		$this->options = get_option( $this->settings_group );
		$ips = $this->format_ips( $this->options['ez_allowed_ips'] );

		# don't block any admins by default
		if( current_user_can( 'activate_plugins' ) )
			$admin = true;

		# if we're in the admin section redirect.
		if( is_admin() )
			$admin = true;

		if( $this->options['ez_redirect_url'] != "" && $admin == false ) {
			if( !in_array( $this->user, $ips ) )
				header("Location: {$this->options['ez_redirect_url']}");
		}

	}

	/**
	 * Explode into an array and return
	 * @param $ips
	 * @return array
	 */
	protected function format_ips( $ips ) {
		# remove any spaces
		$ips = str_replace(' ','', $ips);

		# make into array
		$array = explode(',',$ips);

		return $array;
	}

}


$ez_development_mode = new Easy_Development_Mode();