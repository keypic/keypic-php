<?php
/*
Plugin Name: NO CAPTCHA Anti-Spam with Keypic
Plugin URI: http://keypic.com/
Description: Keypic is quite possibly the best way in the world to <strong>protect your blog from comment and trackback spam</strong>.
Version: 1.1.0
Author: Keypic
Author URI: http://keypic.com
License: GPLv2 or later
*/

/*  Copyright 2010-2011  Keypic LLC  (email : info@keypic.com)

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

if(!defined('KEYPIC_PLUGIN_BASENAME')) define('KEYPIC_PLUGIN_BASENAME', plugin_basename(__FILE__));
if(!defined('KEYPIC_PLUGIN_NAME')) define('KEYPIC_PLUGIN_NAME', trim(dirname(KEYPIC_PLUGIN_BASENAME), '/' ));
if(!defined('KEYPIC_PLUGIN_DIR')) define('KEYPIC_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . KEYPIC_PLUGIN_NAME);
if(!defined('KEYPIC_PLUGIN_URL')) define('KEYPIC_PLUGIN_URL', WP_PLUGIN_URL . '/' . KEYPIC_PLUGIN_NAME);
if(!defined('KEYPIC_PLUGIN_MODULES_DIR')) define('KEYPIC_PLUGIN_MODULES_DIR', KEYPIC_PLUGIN_DIR . '/modules');
define('KEYPIC_VERSION', '1.1.1');

// Make sure we don't expose any info if called directly
if(!function_exists('add_action')){echo "Hi there!  I'm just a plugin, not much I can do when called directly."; exit;}

if(is_admin()){require_once dirname( __FILE__ ) . '/admin.php';}

//*********************************************************************************************
//* init
//*********************************************************************************************

function keypic_init()
{
	global $wp_version, $keypic_details;

	$keypic_details = get_option('keypic_details');

	$keypic_version = isset($keypic_details['KEYPIC_VERSION']) ? $keypic_details['KEYPIC_VERSION'] : '0.0.0' ;

	switch(version_compare(KEYPIC_VERSION, $keypic_version))
	{
		case -1 :
//			echo "version_compare -1"; // in this case i don't know what to do :) appear user did a downgrade
		break;

		case 0 :
//			echo "version_compare 0"; // no changes the installed plugin is not changed
		break;

		case 1 :
//			echo "version_compare 1"; // The plugin installed is updated, or it is a fresh installation
			if($keypic_details['KEYPIC_VERSION'] == '0.0.0')
			{
				$keypic_details['KEYPIC_VERSION'] = KEYPIC_VERSION;
				$keypic_details['login']['enabled'] = 0;
				$keypic_details['register']['enabled'] = 1;
				$keypic_details['lostpassword']['enabled'] = 1;
				$keypic_details['comments']['enabled'] = 1;
				$keypic_details['contact_form_7']['enabled'] = 1;
				update_option('keypic_details', $keypic_details);
			}
		break;
	}

//print_r($keypic_details);
//update_option('keypic_details', $keypic_details);
//delete_option('keypic_details');


	if($keypic_details['login']['enabled'] == 1)
	{
		add_action('login_form','keypic_login_form');
		add_filter('authenticate', 'keypic_login_post', 10, 3);
	}

	if($keypic_details['register']['enabled'] == 1)
	{
		add_action('register_form','keypic_register_form');
		add_action('register_post','keypic_register_post', 10, 3);
		add_action( 'user_register', 'keypic_user_register', 10, 1);
	}

	if($keypic_details['lostpassword']['enabled'] == 1)
	{
		add_action('lostpassword_form', 'keypic_lostpassword_form');
		add_action('lostpassword_post','keypic_lostpassword_post');
	}

	if($keypic_details['comments']['enabled'] == 1)
	{
		add_action('comment_form','keypic_comment_form');
		add_action( 'wp_insert_comment', 'keypic_comment_post', 10, 2 );
		add_filter('manage_edit-comments_columns', 'keypic_manage_edit_comments_columns');
		add_filter('manage_comments_custom_column', 'keypic_manage_comments_custom_column', 10, 2);
		add_filter('manage_users_columns', 'keypic_manage_users_columns');
		add_filter('manage_users_custom_column', 'keypic_manage_users_custom_column', 10, 3);
	}
/*
	if($keypic_details['contact_form_7']['enabled'] == 1)
	{
		add_filter('authenticate', 'keypic_login_post', 10, 3);
	}
*/
	$FormID = isset($keypic_details['FormID']) ? $keypic_details['FormID'] : '';

	Keypic::setFormID($FormID);
	Keypic::setUserAgent("User-Agent: WordPress/{$wp_version} | Keypic/" . constant('KEYPIC_VERSION'));
}
add_action('init', 'keypic_init');


//*********************************************************************************************
//* Loading modules
//*********************************************************************************************
add_action('plugins_loaded', 'keypic_load_modules', 1);

function keypic_load_modules()
{
	$dir = KEYPIC_PLUGIN_MODULES_DIR;

	if(!(is_dir($dir) && $dh = opendir($dir)))
		return false;

	while(($module = readdir($dh)) !== false)
	{
		if(substr($module, -4) == '.php')
			include_once $dir . '/' . $module;
	}
}

//*********************************************************************************************
//* Login form
//*********************************************************************************************

function keypic_login_form()
{
	global $keypic_details;

	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$Token = Keypic::getToken($Token);

	$keypic_details_login = $keypic_details['login'];

	// http://www.mywebsite.tld/wp-login.php?exclude_keypic=true
	$exclude_keypic = isset($_GET['exclude_keypic']) ? $_GET['exclude_keypic'] : '';
	if($exclude_keypic)
	{
		$exclude_keypic = '<input type="hidden" name="exclude_keypic" value="true" />';
	}

	$response = '
<p>
 <label style="display: block; margin-bottom: 5px;">
  ' . $exclude_keypic . '
  <input type="hidden" name="Token" value="'.$Token.'" />
  ' . Keypic::getIt($keypic_details_login['RequestType'], $keypic_details_login['WeighthEight']) . '
 </label>
</p>
';

	echo $response;
}
//add_action('login_form','keypic_login_form');

function keypic_login_post($user, $username, $password)
{
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

		$exclude_keypic = isset($_POST['exclude_keypic']) ? $_POST['exclude_keypic'] : '';
		if($exclude_keypic){return 0;}

		$spam = Keypic::isSpam($Token, null, $username, $ClientMessage = '', $ClientFingerprint = '');

		if(!is_numeric($spam) || $spam > Keypic::getSpamPercentage())
		{
			remove_action('authenticate', 'wp_authenticate_username_password', 20);

			if(is_numeric($spam)){$error = sprintf(__('This request has %s&#37; of spam'), $spam);}
			else{$error = __('We are sorry, your Keypic token is not valid');}

			add_filter('shake_error_codes', 'keypic_login_error_shake');
			return new WP_Error('denied', '<strong>SPAM</strong>: ' . $error);
		}
	}
}
//add_filter('authenticate', 'keypic_login_post', 10, 3); // TODO: used also from Contact Form 7, make it better		




function keypic_login_error_shake($shake_codes)
{
	$shake_codes[] = 'denied';
	return $shake_codes;
}

//*********************************************************************************************
//* Register form
//*********************************************************************************************

function keypic_register_form()
{
	global $keypic_details;

	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$Token = Keypic::getToken($Token);

	$keypic_details_register = $keypic_details['register'];

	$response = '
<p>
 <label style="display: block; margin-bottom: 5px;">
  <input type="hidden" name="Token" value="'.$Token.'" />
  ' . Keypic::getIt($keypic_details_register['RequestType'], $keypic_details_register['WeighthEight']) . '
 </label>
</p>
';

	echo $response;
}
//add_action('register_form','keypic_register_form');

function keypic_register_post($login, $email, $errors)
{
	global $spam;

	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$spam = Keypic::isSpam($Token, $email, $login, $ClientMessage = '', $ClientFingerprint = '');

	if(!is_numeric($spam) || $spam > Keypic::getSpamPercentage())
	{
		$errors->add('empty_token', "<strong>ERROR</strong>: We are sorry, your Keypic token is not valid");
	}

}
//add_action('register_post','keypic_register_post', 10, 3);


function keypic_user_register($user_id)
{
	global $spam;

	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$keypic_users = get_option('keypic_users');
	$keypic_users = keypic_remove_old_tokens($keypic_users);
	$keypic_users[$user_id] = array('token' => $Token, 'ts' => time(), 'spam' => $spam);
	update_option('keypic_users', $keypic_users);
}
//add_action( 'user_register', 'keypic_user_register', 10, 1);


function keypic_remove_old_tokens($old_tokens)
{
	$new_tokens = array();
	$three_days_ago = (time() - (60*60*24*3));
	foreach($old_tokens as $k => $v)
	{
		if($v['ts'] > $three_days_ago){$new_tokens[$k] = $v;}
	}
	return $new_tokens;
}

//*********************************************************************************************
//* lost password form
//*********************************************************************************************

function keypic_lostpassword_form()
{
	global $keypic_details;
	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$Token = Keypic::getToken($Token);

	$keypic_details_lostpassword = $keypic_details['lostpassword'];



	$response = '
<p>
 <label style="display: block; margin-bottom: 5px;">
  <input type="hidden" name="Token" value="'.$Token.'" />
  ' . Keypic::getIt($keypic_details_lostpassword['RequestType'], $keypic_details_lostpassword['WeighthEight']) . '
 </label>
</p>
';

	echo $response;
}
//add_action('lostpassword_form', 'keypic_lostpassword_form');

function keypic_lostpassword_post()
{
	global $Token, $keypic_details;

}
//add_action('lostpassword_post','keypic_lostpassword_post');

//*********************************************************************************************
//* Comment form
//*********************************************************************************************

function keypic_comment_form()
{
	global $Token, $keypic_details;
	$Token = Keypic::getToken($Token);

	$keypic_details_comments = $keypic_details['comments'];

	$response = '
<p>
 <label style="display: block; margin-bottom: 5px;">
  <input type="hidden" name="Token" value="'.$Token.'" />
  ' . Keypic::getIt($keypic_details_comments['RequestType'], $keypic_details_comments['WeighthEight']) . '
 </label>
</p>
';

	echo $response;
}
//add_action('comment_form','keypic_comment_form');

function keypic_comment_post($id, $comment)
{
	$Token = isset($_POST['Token']) ? $_POST['Token'] : '';

	$spam = Keypic::isSpam($Token, $comment->comment_author_email, $comment->comment_author, $comment->comment_content, $ClientFingerprint = '');

	$keypic_comments = get_option('keypic_comments');
	$keypic_comments = keypic_remove_old_tokens($keypic_comments);
	$keypic_comments[$comment->comment_ID] = array('token' => $_POST['Token'], 'ts' => time(), 'spam' => $spam);
	update_option('keypic_comments', $keypic_comments);

	if(!is_numeric($spam) || $spam > Keypic::getSpamPercentage())
	{
		wp_spam_comment($comment->comment_ID);
	}
}
//add_action( 'wp_insert_comment', 'keypic_comment_post', 10, 2 );

function keypic_manage_edit_comments_columns($columns)
{
	global $keypic_comments;
	$keypic_comments = get_option('keypic_comments');
	$columns['spam_status'] = __('Keypic SPAM Status');
	return $columns;
}
//add_filter('manage_edit-comments_columns', 'keypic_manage_edit_comments_columns');

function keypic_manage_comments_custom_column($column, $id)
{
	global $keypic_comments;

	$comments = '';
	$comments = isset($keypic_comments[$id]) ? $keypic_comments[$id] : '';
	$spam = isset($comments['spam']) ? $comments['spam'] : '';

	if('spam_status' == $column)
	{
		if($spam)
		{
			if($spam > Keypic::getSpamPercentage()){echo "<b><a href=\"admin.php?action=keypic_report_spam_and_delete_comment&id=$id\" style=\"color:red;\" onclick=\"return confirm('" . __('Are you sure you want to delete this user?') . "')\">" . sprintf(__('Report SPAM and Delete (spam %s&#37;)'), $spam) . "</a></b>";}
			else{echo "<a href=\"admin.php?action=keypic_report_spam_and_delete_comment&id=$id\" onclick=\"return confirm('" . __('Are you sure you want to delete this user?') . "')\">" . sprintf(__('Report SPAM and Delete (spam %s&#37;)'), $spam) . "</a>";}
		}
	}
}
//add_filter('manage_comments_custom_column', 'keypic_manage_comments_custom_column', 10, 2);

function keypic_manage_users_columns($columns)
{
	global $keypic_users;
	$keypic_users = get_option('keypic_users');
	$columns['spam_status'] = __('Keypic SPAM Status');
 	return $columns;

}
//add_filter('manage_users_columns', 'keypic_manage_users_columns');

function keypic_manage_users_custom_column($empty='', $column_name, $id)
{
	$return = '';
	global $keypic_users;
	$users = '';
	$users = isset($keypic_users[$id]) ? $keypic_users[$id] : '';
	$spam = isset($users['spam']) ? $users['spam'] : '';

	if($spam)
	{
		if($spam > Keypic::getSpamPercentage()){$return .= "<b><a href=\"admin.php?action=keypic_report_spam_and_delete_user&id=$id\" style=\"color:red;\" onclick=\"return confirm('" . __('Are you sure you want to delete this user?') . "')\">" . sprintf(__('Report SPAM and Delete (spam %s&#37;)'), $spam) . "</a></b>";}
		else{$return .= "<a href=\"admin.php?action=keypic_report_spam_and_delete_user&id=$id\" onclick=\"return confirm('" . __('Are you sure you want to delete this user?') . "')\">" . sprintf(__('Report SPAM and Delete (spam %s&#37;)'), $spam) . "</a>";}

	}

	return $return;
}
//add_filter('manage_users_custom_column', 'keypic_manage_users_custom_column', 10, 3);

function keypic_get_select_weightheight($select_name='', $select_value = '')
{

	$options = array(
	'' => '',
	'1x1' => 'Lead square transparent 1x1 pixel',
	'336x280' => 'Large rectangle (336 x 280)',
	'300x250' => 'Medium Rectangle (300 x 250)',
	'250x250' => 'Square Pop-Up (250 x 250)',
	'240x400' => 'Vertical Rectangle (240 x 400)',
	'180x150' => 'Rectangle (180 x 150)',
	'300x100' => '3:1 Rectangle (300 x 100)',
	'720x300' => 'Pop-under (720 x 300)',
	'392x72' => 'Banner w/Naw Bar (392 x 72)',
	'468x60' => 'Full Banner (468 x 60)',
	'234x60' => 'Half Banner (234 x 60)',
	'80x15' => 'Micro Button (80 x 15)',
	'88x31' => 'Micro Bar (88 x 31)',
	'120x90' => 'Button 1 (120 x 90)',
	'120x60' => 'Button 2 (120 x 60)',
	'120x240' => 'Vertical Banner (120 x 240)',
	'125x125' => 'Square Button (125 x 125)',
	'728x90' => 'Leaderboard (728 x 90)',
	'120x600' => 'Skyscraper (120 x 600)',
	'160x600' => 'Wide Skyscraper (160 x 600)',
	'300x600' => 'Half Page Ad (300 x 600)'
	);

	$return = '<select name="'.$select_name.'" onChange="submit();">';
	foreach($options as $k => $v)
	{
		if($select_value == $k){$return .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';}
		else{$return .= '<option value="'.$k.'">'.$v.'</option>';}
	}

	$return .= '</select>';

	return $return;
}

function keypic_get_select_requesttype($select_name='', $select_value = '')
{
	$options = array(
	'getImage' => 'getImage',
	'getiFrame' => 'getiFrame'
	);

	$return = '<select name="'.$select_name.'" onChange="submit();">';
	foreach($options as $k => $v)
	{
		if($select_value == $k){$return .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';}
		else{$return .= '<option value="'.$k.'">'.$v.'</option>';}
	}

	$return .= '</select>';

	return $return;
}

function keypic_get_select_enabled($select_name='', $select_value = '')
{
	$options = array(
	1 => 'Enabled',
	0 => 'Disabled'
	);

	$return = '<select name="'.$select_name.'" onChange="submit();">';
	foreach($options as $k => $v)
	{
		if($select_value == $k){$return .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';}
		else{$return .= '<option value="'.$k.'">'.$v.'</option>';}
	}

	$return .= '</select>';

	return $return;
}


class Keypic
{
	private static $Instance;
	private static $version = '1.4';
	private static $UserAgent = 'User-Agent: Keypic PHP5 Class, Version: 1.4';
	private static $SpamPercentage = 70;
	private static $host = 'ws.keypic.com'; // ws.keypic.com
	private static $url = '/';
	private static $port = 80;

	private static $FormID;
	private static $Token;
	private static $RequestType;
	private static $WeightHeight;
	private static $Debug;

	private function __clone(){}

	public function __construct(){}

	public static function getInstance()
	{
		if (!self::$Instance)
		{
			self::$Instance = new self();
		}

		return self::$Instance;
	}


	public static function getHost(){return self::$host;}

	public static function setHost($host){self::$host = $host;}

	public static function getSpamPercentage(){return self::$SpamPercentage;}

	public static function setSpamPercentage($SpamPercentage){self::$SpamPercentage = $SpamPercentage;}

	public static function setVersion($version){self::$version = $version;}

	public static function getVersion(){return self::$version;}

	public static function setUserAgent($UserAgent){self::$UserAgent = $UserAgent;}

	public static function setFormID($FormID){self::$FormID = $FormID;}

	public static function setDebug($Debug){self::$Debug = $Debug;}

	public static function checkFormID($FormID)
	{
		$fields['RequestType'] = 'checkFormID';
		$fields['ResponseType'] = '2';
		$fields['FormID'] = $FormID;

		$response = json_decode(self::sendRequest($fields), true);
		return $response;
	}

	// makes a request to the Keypic Web Service
	private static function sendRequest($fields)
	{
		// boundary generation
		srand((double)microtime()*1000000);
		$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

		// Build the header
		$header = "POST " . self::$url . " HTTP/1.0\r\n";
		$header .= "Host: " . self::$host . "\r\n";
		$header .= "Content-type: multipart/form-data, boundary=$boundary\r\n";
		$header .= self::$UserAgent . "\r\n";


		$data = '';
		// attach post vars
		foreach($fields AS $index => $value)
		{
			$data .="--$boundary\r\n";
			$data .= "Content-Disposition: form-data; name=\"$index\"\r\n";
			$data .= "\r\n$value\r\n";
			$data .="--$boundary\r\n";
		}

		// and attach the file
//		$data .= "--$boundary\r\n";
//		$content_file = join("", file($tmp_name));
//		$data .="Content-Disposition: form-data; name=\"userfile\"; filename=\"$file_name\"\r\n";
//		$data .= "Content-Type: $content_type\r\n\r\n";
//		$data .= "$content_file\r\n";
//		$data .="--$boundary--\r\n";

		$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

		$socket = new Socket(self::$host, self::$port, $header.$data);
		$socket->send();
		$return = explode("\r\n\r\n", $socket->getResponse(), 2);
		return $return[1];
	}

	public static function getToken($Token, $ClientEmailAddress = '', $ClientUsername = '', $ClientMessage = '', $ClientFingerprint = '', $Quantity = 1)
	{
		if($Token)
		{
			self::$Token = $Token;
			return self::$Token;
		}
		else
		{

			$fields['FormID'] = self::$FormID;
			$fields['RequestType'] = 'RequestNewToken';
			$fields['ResponseType'] = '2';
			$fields['Quantity'] = $Quantity;
			$fields['ServerName'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
			$fields['ClientIP'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
			$fields['ClientUserAgent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$fields['ClientAccept'] = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
			$fields['ClientAcceptEncoding'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
			$fields['ClientAcceptLanguage'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
			$fields['ClientAcceptCharset'] = isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : '';
			$fields['ClientHttpReferer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$fields['ClientUsername'] = $ClientUsername;
			$fields['ClientEmailAddress'] = $ClientEmailAddress;
			$fields['ClientMessage'] = $ClientMessage;
			$fields['ClientFingerprint'] = $ClientFingerprint;

			$response = json_decode(self::sendRequest($fields), true);

			if($response['status'] == 'new_token')
			{
				self::$Token = $response['Token'];
				return  $response['Token'];
			}
		}
	}

	public static function getIt($RequestType = 'getImage', $WeightHeight = '88x31', $Debug = null)
	{

		if($RequestType == 'getiFrame')
		{
			if($WeightHeight)
			{
				$xy = explode('x', $WeightHeight);
				$x = (int)$xy[0];
				$y = (int)$xy[1];
			}
			else{$x=88; $y=31;}

			$url = 'http://' . self::$host . '/?RequestType=getiFrame&amp;WeightHeight=' . $WeightHeight . '&amp;Token=' . self::$Token;

		return <<<EOT
	<iframe
	src="$url"
	width="$x"
	height="$y"
	frameborder="0"
	style="border: 0px solid #ffffff; background-color: #ffffff;"
	marginwidth="0"
	marginheight="0"
	vspace="0"
	hspace="0"
	allowtransparency="true"
	scrolling="no"><p>Your browser does not support iframes.</p></iframe>
EOT;

		}
		else
		{
			return '<a href="http://' . self::$host . '/?RequestType=getClick&amp;Token=' . self::$Token . '" target="_blank"><img src="http://' . self::$host . '/?RequestType=getImage&amp;Token=' . self::$Token . '&amp;WeightHeight=' . $WeightHeight . '&amp;Debug=' . self::$Debug . '" alt="Form protected by Keypic" /></a>';
		}

	}

	// Is Spam? from 0% to 100%
	public static function isSpam($Token, $ClientEmailAddress = '', $ClientUsername = '', $ClientMessage = '', $ClientFingerprint = '')
	{
		self::$Token = $Token;
		$fields['Token'] = self::$Token;
		$fields['FormID'] = self::$FormID;
		$fields['RequestType'] = 'RequestValidation';
		$fields['ResponseType'] = '2';
		$fields['ServerName'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
		$fields['ClientIP'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$fields['ClientUserAgent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$fields['ClientAccept'] = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
		$fields['ClientAcceptEncoding'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
		$fields['ClientAcceptLanguage'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$fields['ClientAcceptCharset'] = isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : '';
		$fields['ClientHttpReferer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$fields['ClientUsername'] = $ClientUsername;
		$fields['ClientEmailAddress'] = $ClientEmailAddress;
		$fields['ClientMessage'] = $ClientMessage;
		$fields['ClientFingerprint'] = $ClientFingerprint;

		$response = json_decode(self::sendRequest($fields), true);

		if($response['status'] == 'response'){return $response['spam'];}
		else if($response['status'] == 'error'){return $response['error'];}
	}

	public static function reportSpam($Token)
	{
		if($Token == ''){return 'error';}
		if(self::$FormID == ''){return 'error';}

		$fields['Token'] = $Token;
		$fields['FormID'] = self::$FormID;
		$fields['RequestType'] = 'ReportSpam';
		$fields['ResponseType'] = '2';

		$response = json_decode(self::sendRequest($fields), true);
		return $response;
	}

}

class Socket
{
	private $host;
	private $port;
	private $request;
	private $response;
	private $responseLength;
	private $errorNumber;
	private $errorString;
	private $timeout;
	private $retry;

	public function __construct($host, $port, $request, $responseLength = 1024, $timeout = 3, $retry = 3)
	{
		$this->host = $host;
		$this->port = $port;
		$this->request = $request;
		$this->responseLength = $responseLength;
		$this->errorNumber = 0;
		$this->errorString = '';
		$this->timeout = $timeout;
		$this->retry = $retry;
	}

	public function Send()
	{
		$this->response = '';
		$r = 0;

		do
		{
			if($r >= $this->retry){return;}

			$fs = fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, $this->timeout);
			++$r;
		}
		while(!$fs);

		if($this->errorNumber != 0){throw new Exception('Error connecting to host: ' . $this->host . ' Error number: ' . $this->errorNumber . ' Error message: ' . $this->errorString);}

		if($fs !== false)
		{
			@fwrite($fs, $this->request);

			while(!feof($fs))
			{
				$this->response .= fgets($fs, $this->responseLength);
			}

			fclose($fs);
			
		}
	}

	public function getResponse(){return $this->response;}

	public function getErrorNumner(){return $this->errorNumber;}

	public function getErrorString(){return $this->errorString;}
}

?>