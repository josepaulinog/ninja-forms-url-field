<?php
if (!defined('ABSPATH')) {
    exit;
}

class NF_URL_Field extends NF_Abstracts_Input {
    protected $_name = 'url';
    protected $_type = 'url';
    protected $_nicename = '';
    protected $_section = 'common';
    protected $_icon = 'link';
    protected $_templates = 'url';

    public function __construct() {
        parent::__construct();
        $this->_nicename = __('URL Field', 'nf-url-field');

        add_filter('ninja_forms_field_template_file_paths', array($this, 'add_field_template_path'));
    }

    public function add_field_template_path($paths) {
        $paths[] = dirname(NF_URL_FIELD_FILE) . '/templates/';
        return $paths;
    }

    public function validate($field, $value) {
        if (isset($field['required']) && $field['required'] && empty($value)) {
            return __('This field is required.', 'nf-url-field');
        }

        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            return __('Please enter a valid URL.', 'nf-url-field');
        }
    }

    public function filter_default_value($default_value, $field_class, $settings) {
        if (!isset($settings['default_type']) ||
            'user-meta' != $settings['default_type'] ||
            $this->_name != $field_class->get_name()) {
            return $default_value;
        }

        $current_user = wp_get_current_user();

        if ($current_user) {
            $default_value = $current_user->user_url;
        }

        return $default_value;
    }
}