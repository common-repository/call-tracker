<?php
/*
Plugin Name: Call Tracker
Plugin URI: #
Description: Easily track where you get calls from with our trackable number widget. Simply add a referrer URL and associate to a trackable number from HitCrowd. All ways know where your calls are coming from. Setting up your call tracking plugin is quick end easy!
Author: HitCrowd
Author URI: #
Version: 1.3
*/

/*  Copyright 2013 HitCrowd (email : pr@hitcrowd.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


include_once('call-tracker-functions.php');
include_once('call-tracker-decorator.php');

class CallTracker { 
	public $folder;
	public $decorator;

	public function __construct() {
		$this->folder = $this->ct_get_plugins_url();
		$this->decorator = new CallTrackerDecorator();

		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->ct_post_render();
		}

		if(!empty($_GET['remove-statistics']) && isset($_GET['page']) && $_GET['page'] == 'statistics') {
			update_option('ct_domain_counter', '');
		}

		add_action('init', array($this, 'init'));
		add_action('admin_menu', array($this, 'ct_register_submenu_page'));
	}

	public function init() {

		if(!isset($_SESSION)) {
			session_start();
		}

		wp_enqueue_style('ct-style', $this->folder . '/css/style.css', null, '1.0');
		wp_enqueue_script('ct-functions', $this->folder . '/js/functions.js', array('jquery'), '1.0');		
	}

	public function ct_register_submenu_page() {
		$parent_menu_slug = 'ct-menu';

		add_menu_page( 
	        'Call Tracker', 
			'Call Tracker',
			'manage_options',
			$parent_menu_slug,
			array($this, 'ct_call_tracker_callback'),
			$this->folder . '/css/images/ct-icon.png'
	    );

		$submenus = array(
			'statistics' => array(
				'title' => 'Statistics',
				'callback' => 'ct_register_submenu_statistics_callback',
			),
			'help' => array(
				'title' => 'Configure',
				'callback' => 'ct_register_submenu_help_callback',
			),
			'video' => array(
				'title' => 'Marketing Video',
				'callback' => 'ct_register_submenu_video_callback',
			)			

		);
		foreach($submenus as $key => $submenu) {
			add_submenu_page( 
				$parent_menu_slug,
				$submenu['title'], 
				$submenu['title'], 
				'manage_options', 
				$key, 
				array($this, $submenu['callback'])
			); 
		}
	}

	public function ct_register_submenu_statistics_callback() {
		$this->decorator->show_statistics_section();
	}

	public function ct_call_tracker_callback() {
		$this->decorator->show_number_section();
	}

	public function ct_register_submenu_video_callback() {
		$this->decorator->show_video_section();
	}	

	public function ct_register_submenu_help_callback() {
		$this->decorator->show_help_section();
	}

	protected function ct_clear_field_text($text) {
		return stripslashes(strip_tags($text));
	}

	private function ct_redirect_to_current_page() {
		header('Location: ' . add_query_arg('success', 'true', $_SERVER['REQUEST_URI']));
		exit();
	}

	private function ct_post_render() {
		
		if(isset($_POST['ct-add-numbers']) && isset($_POST['popular_referrer_links']) && isset($_POST['popular_referrer_phones']) ) {
			$this->cn_update_list_option($_POST['popular_referrer_links'], $_POST['popular_referrer_phones'], 'ct_popular_referrer_phones');
		}

		if(isset($_POST['ct-add-numbers']) && isset($_POST['custom_referrer_links']) && isset($_POST['custom_referrer_phones'])) {
			$this->cn_update_list_option($_POST['custom_referrer_links'], $_POST['custom_referrer_phones'], 'ct_custom_referrer_phones');
		}

		if(isset($_POST['ct-toll-free-number'])) {
			update_option('ct_toll_free_phone_number', $this->ct_clear_field_text($_POST['ct-toll-free-number']));
			
		}
		
		if(isset($_POST['ct-local-number'])) {
			update_option('ct_local_phone_number', $this->ct_clear_field_text($_POST['ct-local-number']));
			
		}

		if(isset($_POST['ct-add-numbers'])) {
			$this->ct_redirect_to_current_page();
		}

	}

	private function cn_update_list_option($links, $phones, $option) {
		$numbers = array();
		$i = 0 ;


		foreach($links as $link) {
			$numbers[$this->ct_clear_field_text($link)] = $this->ct_clear_field_text($phones[$i]);
			$i++;
		}

		update_option($option, $numbers);
	}

	public function ct_get_referrer_list() {
		$list = array(
			'Google Adwords (SEM)' => 'Google Adwords (SEM)',
			'Google Organic (SEO)' => 'Google Organic (SEO)',
			'Bing Ads' => 'Bing Ads',
			'Yelp' => 'Yelp',
			'Twitter' => 'Twitter',
			'Yahoo' => 'Yahoo',
			'Facebook' => 'Facebook',		
			'Tumblr' => 'Tumblr',		
			'FourSquare' => 'FourSquare',		
			'LinkedIn' => 'LinkedIn',		
			'Merchant Circle' => 'Merchant Circle',	

		);
		return $list;
	}

	public function ct_get_current_number($local = false) {


		if(!empty($_SESSION['ct_session_number'])) {
			return $_SESSION['ct_session_number'];
		}

		$custom_numbers = $this->ct_get_custom_referrer_phones();
		$popular_numbers = $this->ct_get_popular_referrer_phones();		
		$domain = false;
		$number = false;

		if(isset($_SERVER['HTTP_REFERER'])) {
			$http_referrer = $_SERVER['HTTP_REFERER'];
			$domain_cointer = get_option('ct_domain_counter');

			$referrer_regex = array(
				'Bing' => '~https?://(www.|)bing.com~i',
				'Google Adwords' => '~https?://(www.)?.*google.*gclid~i',
				'Google (organic)' => '~https?://(www.)google.*~i',
				'Yelp' => '~https?://(www.|)yelp.com~i',
				'Twitter' => '~https?://(www.|)(twitter|t.co)~i',		
				'Yahoo' => '~https?://(www.|)yahoo.com~i',		
				'Facebook' => '~https?://(www.|)facebook.com~i',		
				'Tumblr' => '~https?://(www.|)tumblr.com~i',		
				'FourSquare' => '~https?://(www.|)foursquare.~i',		
				'LinkedIn' => '~https?://(www.|)linkedin.com~i',		
				'Merchant Circle' => '~https?://(www.|)merchantcircle.com~i',		
			);
			foreach($referrer_regex as $key => $referrer) { 
				if(preg_match($referrer, $http_referrer)) {
					$domain_name = $key;
					if(!empty($popular_numbers[$domain_name])) {
						$number = $popular_numbers[$domain_name];
						$domain = $domain_name;
						break;
					}
				} 
			}

			if(!$domain) {
				if(!preg_match('~^https?://~i', $http_referrer)) {
					$http_referrer = 'http://' . $http_referrer;
				}

				$parse_referer_url = parse_url($http_referrer);
				foreach($custom_numbers as $key => $custom_number) { 

					if(preg_match('~' . preg_quote(str_replace('www.', '', $parse_referer_url['host']), '~') . '~i', str_replace('www.', '', $key) ) ) {

						$number = $custom_number;
						$domain = $key; 
						break;
					}
				}

			}

			if($domain) {

				if(isset($domain_cointer[$domain])) {
					if(empty($_SESSION['ct-count-domain'])) {
						$domain_cointer[$domain] = $domain_cointer[$domain] + 1;
					}
				} else {
					$domain_cointer[$domain] = 1;
				}

				$_SESSION['ct-count-domain'] = 'yes';

				update_option('ct_domain_counter', $domain_cointer);
			}


		}


		if($number) {
			$_SESSION['ct_session_number'] = $number;			
		}

 		if(!$number && $local) {
			$number = $this->ct_get_local_number();
		} elseif(!$number && !$local) {
			$number = $this->ct_get_toll_free_number();
		}

		return $number;
	}

	public function ct_get_toll_free_number() {
		return get_option('ct_toll_free_phone_number');
	}	

	public function ct_get_local_number() {
		return get_option('ct_local_phone_number');
	}	

	public function ct_get_domain_statistcs_counter() {
		$domains = get_option('ct_domain_counter');
		if ($domains) {
			arsort($domains);
		}
		
		return $domains;
	}	

	public function ct_get_popular_referrer_phones() {
		return get_option('ct_popular_referrer_phones');
	}

	public function ct_get_custom_referrer_phones() {
		return get_option('ct_custom_referrer_phones');
	}

	public function ct_get_plugins_url() {
		return plugins_url(basename(dirname(__FILE__)));
	}

}

global $call_tracker;
$call_tracker = new CallTracker();