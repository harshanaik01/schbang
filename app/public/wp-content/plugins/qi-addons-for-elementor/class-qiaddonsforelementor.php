<?php
/**
 * Plugin Name: Qi Addons for Elementor
 * Description: Qi Addons for Elementor is a comprehensive library of 60 custom, flexible & easily styled widgets for Elementor developed by Qode Interactive.
 * Author: Qode Interactive
 * Author URI: https://qodeinteractive.com/
 * Plugin URI: https://qodeinteractive.com/qi-addons-for-elementor/
 * Version: 1.9.2
 * Text Domain: qi-addons-for-elementor
 * Elementor tested up to: 3.29.2
 * Elementor Pro tested up to: 3.29.2
 */

if ( ! class_exists( 'QiAddonsForElementor' ) ) {
	class QiAddonsForElementor {
		private static $instance;
		public $swiper_version;

		public function __construct() {
			$this->before_init();
			$this->swiper_version = get_option( 'qi_addons_for_elementor_swiper_new' ) == 'no' ? '5.4.5' : '8.4.5';

			add_action( 'qi_addons_for_elementor_action_framework_load_dependent_plugins', array( $this, 'init' ) );
		}

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function before_init() {
			// constant is defined here because it's not possible to get main plugin file name from constant.php ( it would return 'constant.php' itself ).
			define( 'QI_ADDONS_FOR_ELEMENTOR_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );

			require_once __DIR__ . '/constants.php';

			require_once QI_ADDONS_FOR_ELEMENTOR_ADMIN_PATH . '/class-qiaddonsforelementor-framework.php';

			if ( false === boolval( get_option( 'qi_addons_for_elementor_install_date' ) ) ) {
				// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				update_option( 'qi_addons_for_elementor_install_date', current_time( 'timestamp' ) );
			}
		}

		public function init() {
			$this->require_core();

			// Include plugin assets.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_additional_assets' ) );
			// priority 11 is because of swiper initialization bug, this script needs to be loaded after 'elementor-frontend' css ( which is loaded on priority 10 ).
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_additional_assets' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ), 15 );

			// Make plugin available for translation.
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 15 );

			// Add plugin's body classes.
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			// Hook to include additional modules when plugin loaded.
			do_action( 'qi_addons_for_elementor_action_plugin_loaded' );
		}

		public function require_core() {
			require_once QI_ADDONS_FOR_ELEMENTOR_ABS_PATH . '/helpers/helper.php';

			// Hook to include additional files before modules inclusion.
			do_action( 'qi_addons_for_elementor_action_before_include_modules' );

			foreach ( glob( QI_ADDONS_FOR_ELEMENTOR_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional files after modules inclusion.
			do_action( 'qi_addons_for_elementor_action_after_include_modules' );
		}

		public function enqueue_assets() {
			// CSS and JS dependency variables.
			$style_dependency_array  = apply_filters( 'qi_addons_for_elementor_filter_style_dependencies', array() );
			$script_dependency_array = apply_filters( 'qi_addons_for_elementor_filter_script_dependencies', array( 'jquery' ) );

			// Hook to include additional scripts before plugin's main style.
			do_action( 'qi_addons_for_elementor_action_before_main_css' );

			// Enqueue plugin's main grid style.
			wp_enqueue_style( 'qi-addons-for-elementor-grid-style', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/css/grid.min.css', array(), QI_ADDONS_FOR_ELEMENTOR_VERSION );

			// Enqueue plugin's main grid style.
			wp_enqueue_style( 'qi-addons-for-elementor-helper-parts-style', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/css/helper-parts.min.css', array(), QI_ADDONS_FOR_ELEMENTOR_VERSION );

			// Enqueue plugin's main style.
			wp_enqueue_style( 'qi-addons-for-elementor-style', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/css/main.min.css', $style_dependency_array, QI_ADDONS_FOR_ELEMENTOR_VERSION );

			// Hook to include additional scripts after plugin's main style.
			do_action( 'qi_addons_for_elementor_action_after_main_css' );

			// Enqueue plugin's 3rd party scripts.
			wp_enqueue_script( 'jquery-ui-core' );
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
			wp_register_script( 'fslightbox', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/plugins/fslightbox/fslightbox.min.js', array(), false, true );
			// because Elementor is forcing it's script to be loaded.
			wp_deregister_script( 'swiper' );
			wp_register_script( 'swiper', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/plugins/swiper/' . $this->swiper_version . '/swiper.min.js', array( 'jquery' ), $this->swiper_version, true );

			// Hook to include additional scripts before plugin's main script.
			do_action( 'qi_addons_for_elementor_action_before_main_js' );

			// Enqueue plugin's main script.
			wp_enqueue_script( 'qi-addons-for-elementor-script', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/js/main.min.js', $script_dependency_array, QI_ADDONS_FOR_ELEMENTOR_VERSION, true );

			// Localize plugin's main script.
			$global = apply_filters(
				'qi_addons_for_elementor_filter_localize_main_js',
				array(
					'adminBarHeight' => is_admin_bar_showing() ? 32 : 0,
					'iconArrowLeft'  => qi_addons_for_elementor_get_svg_icon( 'slider-arrow-left' ),
					'iconArrowRight' => qi_addons_for_elementor_get_svg_icon( 'slider-arrow-right' ),
					'iconClose'      => qi_addons_for_elementor_get_svg_icon( 'close' ),
				)
			);

			wp_localize_script(
				'qi-addons-for-elementor-script',
				'qodefQiAddonsGlobal',
				array(
					'vars' => $global,
				)
			);

			// Hook to include additional scripts after plugin's main script.
			do_action( 'qi_addons_for_elementor_action_after_main_js' );
		}

		public function register_additional_assets() {
			// because Elementor is forcing it's style to be loaded.
			wp_deregister_style( 'swiper' );
			wp_register_style( 'swiper', QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/plugins/swiper/' . $this->swiper_version . '/swiper.min.css', array(), $this->swiper_version );
		}

		public function enqueue_additional_assets() {
			wp_enqueue_style( 'swiper' );
		}

		public function add_inline_style() {
			$style = apply_filters( 'qi_addons_for_elementor_filter_add_inline_style', $style = '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( apply_filters( 'qi_addons_for_elementor_filter_inline_style_handle', 'qi-addons-for-elementor-style' ), $style );
			}
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'qi-addons-for-elementor', false, QI_ADDONS_FOR_ELEMENTOR_REL_PATH . '/languages' );
		}

		public function add_body_classes( $classes ) {
			$classes[] = 'qi-addons-for-elementor-' . QI_ADDONS_FOR_ELEMENTOR_VERSION;

			return $classes;
		}
	}

	QiAddonsForElementor::get_instance();
}

if ( ! function_exists( 'qi_addons_for_elementor_activation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin activation
	 */
	function qi_addons_for_elementor_activation_trigger() {

		// Hook to add additional code on plugin activation.
		do_action( 'qi_addons_for_elementor_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'qi_addons_for_elementor_activation_trigger' );
}

if ( ! function_exists( 'qi_addons_for_elementor_deactivation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin deactivation
	 */
	function qi_addons_for_elementor_deactivation_trigger() {

		// Hook to add additional code on plugin deactivation.
		do_action( 'qi_addons_for_elementor_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'qi_addons_for_elementor_deactivation_trigger' );
}

if ( ! function_exists( 'qi_addons_for_elementor_add_placeholder_image' ) ) {
	/**
	 * Create a placeholder image in the media library
	 */
	function qi_addons_for_elementor_add_placeholder_image() {

		if ( qi_addons_for_elementor_framework_is_installed( 'elementor' ) ) {
			$placeholder = get_option( 'qi_addons_for_elementor_placeholder_image' );

			// if placeholder option does not exists, enter empty value.
			if ( ! $placeholder ) {
				update_option(
					'qi_addons_for_elementor_placeholder_image',
					array(
						'id'  => '',
						'url' => '',
					)
				);
			}

			// Check if there is already an option and is attachment.
			if ( ! empty( $placeholder ) ) {
				if ( is_int( $placeholder['id'] ) && wp_attachment_is_image( $placeholder['id'] ) ) {
					return;
				}
			}

			$upload_dir = wp_upload_dir();
			$source     = QI_ADDONS_FOR_ELEMENTOR_URL_PATH . 'assets/img/placeholder.png';
			$filename   = $upload_dir['basedir'] . '/qi-addons-for-elementor-placeholder.png';

			if ( ! file_exists( $filename ) ) {
				copy( $source, $filename ); // @codingStandardsIgnoreLine.
			}

			if ( ! file_exists( $filename ) ) {
				return;
			}

			$filetype   = wp_check_filetype( basename( $filename ), null );
			$attachment = array(
				'guid'           => $upload_dir['url'] . '/' . basename( $filename ),
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			$attach_id  = wp_insert_attachment( $attachment, $filename );

			if ( is_int( $attach_id ) && 0 !== $attach_id ) {
				update_option(
					'qi_addons_for_elementor_placeholder_image',
					array(
						'id'  => $attach_id,
						'url' => wp_get_attachment_image_url( $attach_id ),
					)
				);

				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );
			}
		}
	}

	add_action( 'qi_addons_for_elementor_action_on_activation', 'qi_addons_for_elementor_add_placeholder_image' );
	add_action( 'qode_essential_addons_action_after_plugin_activation_qi-addons-for-elementor', 'qi_addons_for_elementor_add_placeholder_image' );
}

if ( ! function_exists( 'qi_addons_for_elementor_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function qi_addons_for_elementor_check_requirements() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'admin_notices', 'qi_addons_for_elementor_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'qi_addons_for_elementor_check_requirements' );
}

if ( ! function_exists( 'qi_addons_for_elementor_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function qi_addons_for_elementor_admin_notice_content() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'Elementor plugin is required for Qi Addons for Elementor plugin to work properly. Please install/activate it first.', 'qi-addons-for-elementor' ) );

		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'qi_addons_for_elementor_regenerate_css' ) ) {
	/**
	 * Function that regenerate Elementor css
	 */
	function qi_addons_for_elementor_regenerate_css() {
		if ( empty( get_option( 'qi_addons_for_elementor_regenerate_css' ) ) ) {
			if ( did_action( 'elementor/loaded' ) ) {
				update_option( 'qi_addons_for_elementor_regenerate_css', '1' );

				// Automatically purge and regenerate the Elementor CSS cache.
				\Elementor\Plugin::instance()->files_manager->clear_cache();
			}
		}
	}

	add_action( 'init', 'qi_addons_for_elementor_regenerate_css' );
}
