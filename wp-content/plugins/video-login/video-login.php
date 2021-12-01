<?php

/**
 * Plugin Name:       Video Login
 * Description:       Show video and allow users to view 1 minute before requiring login
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            David Maksimov
 * Text Domain:       video-login
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class VideoLoginPlugin
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueAssets'));
        add_action('init', array($this, 'initShortcode'));
        add_action('wp_ajax_nopriv_video_login', array($this, 'doAjaxLogin'));
        add_action('wp_footer', array($this, 'insertLoginModal'));
    }

    public function enqueueAssets()
    {
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'));
        wp_enqueue_script('wistia', 'https://fast.wistia.com/assets/external/E-v1.js');

        if (! is_user_logged_in()) {
            wp_enqueue_script('video-login', plugins_url('/public/js/video-login.js', __FILE__));
            wp_localize_script(
                'video-login',
                'videoLoginAjax',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('video_login'),
                )
            );
        }
    }

    public function initShortcode()
    {
        add_shortcode('wistia-video', array($this, 'wistiaVideoShortcode'));
    }

    public function wistiaVideoShortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => 'szmzwjd7jt',
        ), $atts, 'wistia-video');

        return "<div class=\"wistia_embed wistia_async_{$atts['id']}\" style=\"width:640px;height:360px;\"></div>";
    }

    public function insertLoginModal()
    {
        if (is_user_logged_in()) {
            return;
        }

        require plugin_dir_path(__FILE__).'public/views/login-modal.php';
    }

    public function doAjaxLogin()
    {
        if (! isset($_POST['_ajax_nonce']) || ! wp_verify_nonce($_POST['_ajax_nonce'], 'video_login')) {
            wp_send_json_error([
                'message' => 'Security check unsuccessful',
            ]);
        }

        if (! isset ($_POST['username']) || empty($_POST['username'])) {
            wp_send_json_error([
                'message' => 'The username field is required',
            ]);
        }

        if (! isset($_POST['password']) || empty($_POST['password'])) {
            wp_send_json_error([
                'message' => 'The password field is required',
            ]);
        }

        $credentials = [
            'user_login' => $_POST['username'],
            'user_password' => $_POST['password'],
        ];
        $user = wp_signon($credentials, false);

        if (is_wp_error($user)) {
            wp_send_json_error([
                'message' => $user->get_error_message(),
            ]);
        }

        wp_send_json_success();
    }
}

$videoLoginPlugin = new VideoLoginPlugin();
