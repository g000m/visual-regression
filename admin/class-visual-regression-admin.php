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

	private $backstop;

	private $viewports;

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

		// create ACF options page. @TODO move this
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( 'option' );
		}

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
	 * get viewports and generate config from saved viewport and default config template and sitemap.xml
	 */
	private function generate_config() {
		require_once WP_PLUGIN_DIR . "/visual-regression/includes/BackstopJSConfig.php";

		if ( function_exists( 'get_field' ) ) {
			$this->viewports = $this->set_viewport_types( get_field( 'scenario', 'option' ) );
		}

		$config = new BackstopJSConfig( get_home_url() . '/sitemap.xml' );

		try {
			$config->generateConfig();
		} catch ( exception $e ) {
			echo "<div>failed to generate config</div>";

			return false;
		}

		$this->generated_config = $config->getConfig();
		$this->generated_config->asyncCaptureLimit = 5;
		$this->generated_config->asyncCompareLimit = 5;

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_setup_page() {

		$this->generate_config();

		echo "Testing URLs:\n";

		$this->backstop = new Backstop_Test_Case( $this->generated_config );

		echo $this->backstop->list_scenarios();

		if ( isset( $_REQUEST['command'] ) ) {

			echo "<div>doing command</div>";
			$response = $this->backstop->handle_command( $_REQUEST['command'] );
//			echo "<div>$response</div>";
//			var_dump( $response );
		}

		?>

		<?php if ( $this->viewports ): ?>
            <div class="vr-settings__scenarios">
                <br>
                <span>Viewports</span>
				<?php
				foreach ( $this->viewports as $viewport ) {
					echo "<div>" . join( $viewport, ', ' ) . "</div>\n";
				}
				?>
                <br>
            </div>
		<?php endif; ?>

        <div class="vr-settings__reference">
            <button id="vr-settings__reference-button" class="vr-settings__reference-button button">Take reference
                snapshots
            </button>
        </div>

        <div class="vr-settings__test">
            <button id="vr-settings__test-button" class="vr-settings__test-button button">Take test snapshots</button>
        </div>

        <script>
			jQuery(function () {
				jQuery("#vr-settings__reference-button").click(() => {
					handleReferenceButton();
				});

				jQuery("#vr-settings__test-button").click(() => {
					handleTestButton();
				});

			});

			function handleReferenceButton() {
				//ajax action
				console.log('reference');
				vr_button_ajax('reference');
			}

			function handleTestButton() {
				//ajax action
				console.log('test');
				vr_button_ajax('test');
			}

			function vr_button_ajax(button_action, test_id = 'default') {
				const url = '<?php echo get_admin_url() . 'admin-ajax.php'; ?>';
				jQuery.post(
					url,
					{
						'action': 'vr-ajax',
						'vr_button_action': button_action
					},
					function (response) {
						console.log('The server responded: ', response);
					}
				);
			}
        </script>
		<?php
	}


	function vr_buttons_ajax_handler() {
		$this->generate_config();

		$this->backstop = new Backstop_Test_Case( $this->generated_config );


		$button = sanitize_text_field( $_REQUEST['vr_button_action'] );

		if ( in_array( $button, [ "reference", "test" ] ) ) {
			$this->backstop->handle_command( $button );
		}


		// Make your response and echo it.
		echo "ajax foobar";

		// Don't forget to stop execution afterward.
		wp_die();
	}

	/**
	 * @param $viewports
	 *
	 * @return array
	 */
	function set_viewport_types( $viewports ) {
		return array_map( function ( $viewport ) {
			return Array(
				"name"   => $viewport['name'],
				"width"  => (int) $viewport['width'],
				"height" => (int) $viewport['height'],
			);
		}, $viewports );
	}
}
