<?php
defined( 'ABSPATH' ) or die('Do not directly call this script');

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option( 'devmode_settings' );