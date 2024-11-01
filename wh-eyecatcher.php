<?php
/*
Plugin Name: WH Eyecatcher
Plugin URI: https://wordpress-handbuch.com/wh-eyecatcher
Description: Add a floating slogan to eyery page of your website 
Version: 1.0.2
Author: WordPress-Handbuch
Author URI: https://wordpress-handbuch.com
License: GPL v2 or later
	Copyright 2019 Richard Eisenmenger
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if( !defined( 'WH_EYECATCHER_VERSION' ) )
	define( 'WH_EYECATCHER_VERSION', '1.0.2' );

class WH_Eyecatcher {
	static $instance = false;
	private function __construct() {
		add_action( 'init', 					array( $this, 'load_textdomain'	) );
		add_action( 'admin_enqueue_scripts',	array( $this, 'admin_scripts' ) );
		add_action( 'admin_init', 				array( $this, 'page_init' ) );
		add_action( 'admin_menu', 				array( $this, 'add_menu' ) );
		add_action( 'admin_footer', 			array( $this, 'show_eyecatcher' ) );
		add_action( 'wp_footer', 				array( $this, 'show_eyecatcher' ) );

		$this->styles = array(
			'Custom'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 5vw; font-weight: bold; line-height: 1.2em; text-align: center;",
			'Style 1'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 6vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(15deg); color: rgba(0,0,0,0.6); text-shadow: 4px 3px 0px #fff, 9px 8px 0px rgba(0,0,0,0.15);",
			'Style 2'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 7vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(12deg); color: transparent; text-shadow: 0 0 5px rgba(255,0,0,0.8);",
			'Style 3'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 9vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(12deg); color: transparent; text-shadow: 0px 0px 10px rgba(100,100,255,0.6), 0px 0px 30px rgba(100,100,255,0.4), 0px 0px 50px rgba(100,100,255,0.3), 0px 0px 180px rgba(100,100,255,0.3); color: rgba(255,255,255,0);",
			'Style 4'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 8vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(12deg); color: transparent; text-shadow: -1px -1px 1px #fff, 1px 1px 1px #000; 	color: transparent;	opacity: 0.8;" ,
			'Style 5'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 9vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(12deg); color: #fff; text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #fff, 0 0 20px #ff2d95, 0 0 30px #ff2d95, 0 0 40px #ff2d95, 0 0 50px #ff2d95, 0 0 75px #ff2d95;	letter-spacing: 5px;",
			'Style 6'	=> "position:fixed; top:4vh; right:4vw; z-index:999999; font-size: 10vw; font-weight: bold; line-height: 1.2em; text-align: center; transform: rotate(5deg); color: #fff; text-shadow: 0px -1px 4px white, 0px -2px 10px yellow, 0px -10px 20px #ff8000, 0px -18px 40px red;"
		);
	}

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wh-eyecatcher', false, basename(dirname(__FILE__)) . '/languages/' );
	}

	public function admin_scripts() {
		wp_enqueue_script( 'wh-eyecatcher-admin', plugins_url('lib/js/admin.js', __FILE__), array(), WH_EYECATCHER_VERSION, 'all' );
	}

	public function page_init() {
		register_setting( 'wh_eyecatcher_option_group', 'wh_eyecatcher_slogan' );
		register_setting( 'wh_eyecatcher_option_group', 'wh_eyecatcher_style' );
		register_setting( 'wh_eyecatcher_option_group', 'wh_eyecatcher_css' );

		add_settings_section( 'wh_eyecatcher_configuration', __('Options', 'wh_eyecatcher'), array( $this, 'print_section_info' ), 'wh-eyecatcher-page' );
		add_settings_field( 'wh_eyecatcher_slogan', 'Slogan', array( $this, 'print_form_field_slogan' ), 'wh-eyecatcher-page', 'wh_eyecatcher_configuration' );
		add_settings_field( 'wh_eyecatcher_style', 'Style', array( $this, 'print_form_field_style' ), 'wh-eyecatcher-page', 'wh_eyecatcher_configuration' );
		add_settings_field( 'wh_eyecatcher_css', 'CSS Style', array( $this, 'print_form_field_css' ), 'wh-eyecatcher-page', 'wh_eyecatcher_configuration' );
	}

	public function add_menu() {
		add_options_page('WH Eyecatcher', 'WH Eyecatcher', 'manage_options', 'wh-eyecatcher-page', array( $this, 'options_page' ));
	}

	public function options_page() {
		echo '
		<div class="wrap">
			<h1>WH Eyecatcher</h1>
			<form action="options.php" method="post">';
		settings_fields( 'wh_eyecatcher_option_group' );
		do_settings_sections( 'wh-eyecatcher-page' );
		submit_button( );
		echo '			</form>
		</div>';
	}

	public function print_section_info() { 
		print __('Here you can configure the slogan to be displayed and how it is being displayed. You can use any CSS styles you like using some sample CSS and/or extending it with own styling ideas.', 'wh-eyecatcher') . basename(dirname(__FILE__)) . '/languages/' . get_locale();
	}

	public function print_form_field_slogan() {
		echo '
		<div>
			<input id="wh_eyecatcher_slogan" type="text" name="wh_eyecatcher_slogan" class="large-text" value="' . esc_attr( get_option('wh_eyecatcher_slogan') ) . '" />
			<p class="description">' . __('Short slogan that will hover above all frontend pages', 'wh-eyecatcher') . '</p>
		</div>';
	}

	public function print_form_field_style() {
		echo '
		<div>
			<select id="wh_eyecatcher_style" name="wh_eyecatcher_style">';
		foreach ( $this->styles as $style => $value ) {
			echo '<option value="' . $value . '" ' . selected( $value, esc_attr( get_option('wh_eyecatcher_style') ) ) . ' onchange>' . $style . '</option>';
		}
		echo '
			</select>
			<p class="description">' . __('Pick a predefined demo style and adjust it. Or start from the scratch using the setting <strong>Custom</strong>. Please note, that CSS styles will be overwritten once you pick a different style here.', 'wh-eyecatcher') . '</p>
		</div>';
	}

	public function print_form_field_css() {
		echo '
		<div>
			<textarea id="wh_eyecatcher_css" name="wh_eyecatcher_css" class="large-text" cols="50" rows="12">' . esc_attr( get_option('wh_eyecatcher_css') ) . '</textarea>
			<p class="description">' . __('CSS styles applied to the slogan - Go crazy!', 'wh-eyecatcher') . '</p>
		</div>';
	}

	public function show_eyecatcher() {
		if (isset($_GET['page']) || (! is_admin())) {
			if (($_GET['page'] === 'wh-eyecatcher-page') || (! is_admin())) {
				echo '<div id="wh-eyecatcher" role="banner" class="wh-eyecatcher wh-eyecatcher-style" style="' . get_option('wh_eyecatcher_css') . '">' . get_option('wh_eyecatcher_slogan') . '</div>';
			}
		}
	}
}

$WH_Eyecatcher = WH_Eyecatcher::getInstance();