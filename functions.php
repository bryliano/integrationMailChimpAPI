<?php

require_once 'vendor/autoload.php';
require_once 'core/MailChimp/SubscribeMailChimp.php';

( new Core\MailChimp\SubscribeMailChimp() );


add_action( 'wp_enqueue_scripts', function() {
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'https://code.jquery.com/jquery-3.5.1.min.js');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'actions', get_stylesheet_directory_uri() . '/actions.js', [], null, true );
} );