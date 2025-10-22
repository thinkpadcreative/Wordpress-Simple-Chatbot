<?php
/**
 * Plugin Name: WP Simple Chatbot
 * Description: A simple chatbot plugin for WordPress.
 * Version: 1.0
 * Author: David Smith - ThinkpadCreative.com
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Enqueue chatbot scripts and styles
function wp_chatbot_enqueue_scripts() {
    wp_enqueue_style('wp-chatbot-style', plugin_dir_url(__FILE__) . 'chatbot.css');
    wp_enqueue_script('wp-chatbot-script', plugin_dir_url(__FILE__) . 'chatbot.js', array('jquery'), null, true);
    wp_localize_script('wp-chatbot-script', 'chatbotAjax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'wp_chatbot_enqueue_scripts');

// Chatbot HTML output function
function wp_chatbot_display() {
    return '<div id="chatbot-container">
                <div id="chatbot-header">Chatbot</div>
                <div id="chatbot-messages"></div>
                <input type="text" id="chatbot-input" placeholder="Type a message...">
                <button id="chatbot-send">Send</button>
            </div>';
}

// Register shortcode to display chatbot
function wp_chatbot_shortcode() {
    return wp_chatbot_display();
}
add_shortcode('wp_chatbot', 'wp_chatbot_shortcode');

// Handle AJAX request
function wp_chatbot_response() {
    if (!isset($_POST['message'])) {
        wp_send_json_error('No message received');
        wp_die();
    }
    
    $user_message = sanitize_text_field($_POST['message']);
    $bot_response = chatbot_generate_response($user_message);
    wp_send_json(array('response' => $bot_response));
    wp_die();
}
add_action('wp_ajax_chatbot_response', 'wp_chatbot_response');
add_action('wp_ajax_nopriv_chatbot_response', 'wp_chatbot_response');

// Improved chatbot logic to detect keywords within messages
function chatbot_generate_response($message) {
    $responses = array(
        'hello' => 'Hi there! How can I assist you?',
        'how are you' => 'I\'m just a bot, but I\'m doing great!',
        'bye' => 'Goodbye! Have a great day!',
        'pitched' => 'Certainly, you can find more information here... <a href="#">Text</a>'
    );
    
    $message_lower = strtolower($message);
    
    foreach ($responses as $keyword => $response) {
        if (strpos($message_lower, $keyword) !== false) {
            return $response;
        }
    }
    
    return 'I\'m not sure how to respond to that.';
}
