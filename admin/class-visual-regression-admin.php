<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gabeherbert.com
 * @since      1.0.0
 *
 * @package    Visual_Regression
 * @subpackage Visual_Regression/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Visual_Regression
 * @subpackage Visual_Regression/admin
 * @author     Gabe Herbert <gh@gabeherbert.com>
 */
class Visual_Regression_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The BackstopJS config object gets stored here
	 * @var     object
	 */
	private $generated_config;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Visual_Regression_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Visual_Regression_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/visual-regression-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Visual_Regression_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Visual_Regression_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/visual-regression-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page( 'Visual Regression Options Settings', 'Visual Regression', 'manage_options', $this->plugin_name, array(
			$this,
			'display_plugin_setup_page'
		) );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 */
	public function add_action_links( $links ) {
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
		);

		return array_merge( $settings_link, $links );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_setup_page() {

		require_once WP_PLUGIN_DIR . "/visual-regression/includes/BackstopJSConfig.php";

		$config = new BackstopJSConfig( get_site_url() . '/sitemap.xml' );
		$config->generateConfig();

		$this->generated_config = $config->getConfig();

		echo "Testing URLs:\n";

		$backstop = new Backstop_Test_Case( $this->generated_config);

		$backstop->list_scenarios();

		if ( isset( $_REQUEST['command'] ) ) {

			echo "<div>doing command</div>";
			$response = $backstop->handle_command( $_REQUEST['command'] );
//			echo "<div>$response</div>";
//			var_dump( $response );
		}
	}

}
