<?php
include dirname(__DIR__). '/order-export/lib/convert_format.php';

/*
Plugin Name: Laber Menu
Plugin URI: http://www.example.com/plugin
Description: メニューの表示
Author: lober system
Version: 0.1
Author URI: http://www.example.com
*/
 
class LaberMenu {

	private $_allow_users;

	/**
	 *
	 **/
	function __construct() {
		$this->set_allow_users();
		add_action('admin_menu', array($this, 'add_pages'));
		add_action('admin_menu', array($this, 'add_sub_menu'));
	}

	/**
	 *
	 **/
	function add_pages() {
		$allow_users = $this->get_allow_users();
		$cur_user = wp_get_current_user();
		if (in_array($cur_user->user_login, $allow_users)) {
			//add_menu_page('労働改善ツール','労働改善ツール',  'level_8', 'lober-tools', array($this,'search_data'), '', 26);
			add_menu_page('労働改善ツール','労働改善ツール',  'level_8', 'lober-tools', array($this,'menu_top'), '', 26);
		}
	}

	/**
	 *
	 **/
	function add_sub_menu() {
		$cur_user = wp_get_current_user();
		if (in_array($cur_user->user_login, $this->_allow_users)) {
			switch ($cur_user->user_login) {
				case 'yamachu':
				case 'ceo':
				case 'admin':
				case 'admin-secret':
					add_submenu_page('lober-tools', 'ig_title','ig_title', 'read', 'information_gathering', array(&$this, 'information_gathering'));
					add_submenu_page('lober-tools', 'ii_title','ii_title', 'read', 'information_import', array(&$this, 'information_import'));
					break;
				default:
					add_submenu_page('lober-tools', 'ig_title','ig_title', 'read', 'information_gathering', array(&$this, 'information_gathering'));
					add_submenu_page('lober-tools', 'ii_title','ii_title', 'read', 'information_import', array(&$this, 'information_import'));
					add_submenu_page('lober-tools', '環境設定','環境設定', 'read', 'environmental_settings', array(&$this, 'environmental_settings'));
					break;
			}
		} else {
			add_submenu_page('lober-tools', '環境設定','環境設定', 'read', 'environmental_settings', array(&$this, 'environmental_settings'));
		}
	}

	/**
	 * メニューTOP
	 **/
	function menu_top() {
		$this->get_users();
		echo '<p>menu_top</p>';
	}

	/**
	 * 
	 **/
	function information_gathering() {
		require(dirname(__DIR__). '/information-gathering/information-gathering.php');
	}

	/**
	 * 
	 **/
	//	  private $_message = array('file_upload' => '');
	function information_import() {
		$prm = $_POST;
		//var_dump($prm);
		if (!empty($prm['cmd_import'])) {
			if (!empty($_FILES)) {
					require(dirname(__DIR__). '/information-import/lib/update_info_mother_cow.php');
					$this->_message['file_upload'] = updateInfoMotherCow();

			} else {
			}

		} else {
		}

		require(dirname(__DIR__). '/information-import/information-import.php');
	}

	/**
	 * 環境設定
	 **/
	function environmental_settings() {
		?><div><?php  phpinfo(); ?></div><?php
	}

	/**
	 *
	 **/
	function set_allow_users() {
		$this->_allow_users = array('root', 'admin', 'user', 'admin-secret');
	}

	/**
	 *
	 **/
	function get_allow_users() {
		return $this->_allow_users;
	}

	/**
	 *
	 **/
	function get_users() {
		global $wpdb;
		$sql  = "SELECT u.user_login FROM wp_users AS u ";
//		$sql .= sprintf("WHERE ap.applicant = '%s' ", $applicant);
		$sql .= "LIMIT 10;";
		$ret_users = $wpdb->get_results($sql);

		foreach ($ret_users as $k => $v) {
			$users[] = $v->user_login;
		}
		$this->vd($users);
	}

	/**
	 *
	 **/
	function vd($d) {
//return false;
		global $wpdb;
		$cur_user = wp_get_current_user();
		if (current($cur_user->roles) == 'administrator') {
			echo '<div class="">';
			echo '<pre>';
//			var_dump($d);
			print_r($d);
			echo '</pre>';
			echo '</div>';
		}
	}
}

$LaberMenu = new LaberMenu;
