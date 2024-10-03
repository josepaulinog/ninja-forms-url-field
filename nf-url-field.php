<?php
/*
 * Plugin Name: URL Field for Ninja Forms
 * Description: Adds a "URL Field" field type for Ninja Forms with improved validation.
 * Version: 1.0.1
 * Author: José Paulino
 * Author URI: https://www.josepaulino.com
 * Text Domain: url-field-for-ninja-forms
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NF_URL_Field_Plugin')) {

    class NF_URL_Field_Plugin {
        private static $instance = null;
        private $version = '1.0.1';

        private function __construct() {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
        }

        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function define_constants() {
            define('NF_URL_FIELD_VERSION', $this->version);
            define('NF_URL_FIELD_PATH', plugin_dir_path(__FILE__));
            define('NF_URL_FIELD_URL', plugin_dir_url(__FILE__));
            define('NF_URL_FIELD_FILE', __FILE__);
        }

        private function includes() {
            require_once NF_URL_FIELD_PATH . 'includes/class-nf-url-field.php';
        }

        private function init_hooks() {
            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
            add_action('ninja_forms_loaded', array($this, 'register_field'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function load_plugin_textdomain() {
            load_plugin_textdomain('nf-url-field', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        public function register_field() {
            Ninja_Forms()->fields['url'] = new NF_URL_Field();
        }

        public function enqueue_scripts() {
            if (function_exists('Ninja_Forms')) {
                wp_enqueue_script('nf-url-field-validation', NF_URL_FIELD_URL . 'js/url-field-validation.js', array('jquery', 'nf-front-end'), NF_URL_FIELD_VERSION, true);
                wp_localize_script('nf-url-field-validation', 'nfURLField', array(
                    'invalidURLMessage' => __('Please enter a valid URL.', 'nf-url-field'),
                    'requiredErrorMessage' => __('This field is required.', 'nf-url-field')
                ));
            }
        }
    }

    function NF_URL_Field() {
        return NF_URL_Field_Plugin::get_instance();
    }

    add_action('plugins_loaded', 'NF_URL_Field');
}