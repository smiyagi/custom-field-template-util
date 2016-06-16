<?php
/**
 * Plugin Name: Custom Field Template Util
 * Version: 1.0
 * Description: This is a plug-in to reset export import of 'Custom Field Template'.
 * Author: Satoshi Miyagi
 * Plugin URI: https://github.com/smiyagi/custom-field-template-util
 * Text Domain: custom-field-template-util
 * Domain Path: /languages
 * License: GPLv2 or later
 * @package Custom-field-template-util
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define('PLUGIN_CFT',        'custom-field-template/custom-field-template.php');
define('PLUGIN_CFT_OPTION', 'custom_field_template_data');
define('CFTU_FILE',         'cft_util-');
define('TOKEN_CFTU_KEY',    'cftu-nonce-key');


class CustomFieldTemplateUtil 
{	
    public function __construct() { 	
        register_activation_hook(__FILE__,   array($this, 'kd_activate'));
		register_deactivation_hook(__FILE__, array($this, 'kd_deactivate'));

		$this->define_hooks();
    }

	public function kd_activate() {}
	public function kd_deactivate() {}

    protected function define_hooks() {
    	add_action('plugins_loaded', array($this, 'cftu_loaded'));
    	add_action('init',           array($this, 'cftu_init'));
    	add_action('admin_menu',     array($this, 'create_menu'));
    	add_action('admin_head',     array($this, 'custom_admin_css'));
    }
	
	public function cftu_loaded() {	
		load_plugin_textdomain('custom-field-template-util', false, basename( dirname( __FILE__ ) ) . '/languages');
	}

	public function cftu_init() {
		$options = $this->get_cft_data();

		if ( isset($_POST['cftu_export_options_submit']) && $this->check_admin() ) {
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=". CFTU_FILE . date('Ymd') . '.txt');
			header('Content-Type: application/octet-stream');
			echo base64_encode(maybe_serialize($options));
			exit();
		}
	}
	
	public function create_menu() {
		add_management_page(
			__("Custom Field Template Util", 'custom-field-template-util'),
			__("Custom Field Template Util", 'custom-field-template-util'),
			'administrator',
			'custom-field-template-util',
			array($this, 'create_admin_page')
		);
	}

	public function create_admin_page() {

		if ( !empty($_POST['cftu_import_options_submit']) && $this->check_admin() ) :
			if ( is_uploaded_file($_FILES['cftfile']['tmp_name']) ) :
				ob_start();
				readfile ($_FILES['cftfile']['tmp_name']);
				$import = ob_get_contents();
				ob_end_clean();
				$import = maybe_unserialize(base64_decode($import));
				update_option(PLUGIN_CFT_OPTION, $import);
				$message = __('Options imported.', 'custom-field-template');
			endif;
		endif;

		if ( !empty($_POST['cftu_reset_options_submit']) && $this->check_admin() ) :
			if (class_exists('cftuExt')) {
				$cft = new cftuExt();
				$cft->reset();
				$message = __('Options resetted.', 'custom-field-template');
			} else {
				$message = __('Options reset failure.', 'custom-field-template-util');
			}
		endif;

		include_once(plugin_dir_path(__FILE__) . 'util.php');
	}

	public function custom_admin_css() {
		wp_enqueue_style( 'cftu-admin', plugin_dir_url(__FILE__) .'style.css');
	}

	private function get_cft_data() {
		$options = get_option(PLUGIN_CFT_OPTION);
		if ( !empty($options) && !is_array($options) ) $options = array();
		return $options;
	}

	private function check_admin() {
		if (!is_user_logged_in() || !isset($_POST['cftu-nonce'])) { return false; }
		if (!check_admin_referer(TOKEN_CFTU_KEY, 'cftu-nonce')) { return false; }
		return true;
	}
}
$cft_util = new CustomFieldTemplateUtil();


add_action('plugins_loaded', 'my_cft_init');
function my_cft_init() {
	if (class_exists('custom_field_template')) {
		class cftuExt extends custom_field_template {
			function reset() {
				$this->install_custom_field_template_data();
				$this->install_custom_field_template_css();
			}
		}
	}
}


