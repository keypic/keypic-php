<?php

/*  Copyright 2010-2014  Keypic Inc.  (email : info@keypic.com)

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

	
	Works only with PHP >= 5.3.0
*/

class Keypic
{
	private static $Instance;
	private static $version = '2.1';
	private static $UserAgent = 'User-Agent: Keypic PHP Class, Version: 2.1';
	private static $SpamPercentage = 70;
	private static $host = 'ws.keypic.com';
	private static $url = '/';

	private static $FormID;
	private static $PublisherID;
	private static $Token;
	private static $RequestType;
	private static $WidthHeight;
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

	public static function getVersion(){return self::$version;}

	public static function setVersion($version){self::$version = $version;}

	public static function setUserAgent($UserAgent){self::$UserAgent = $UserAgent;}

	public static function setFormID($FormID){self::$FormID = $FormID;}

	public static function getFormID(){return self::$FormID;}

	public static function setPublisherID($PublisherID){self::$PublisherID = $PublisherID;}

	public static function setDebug($Debug){self::$Debug = $Debug;}

	public static function checkFormID($FormID)
	{
		$fields['RequestType'] = 'checkFormID';
		$fields['ResponseType'] = '2';
		$fields['FormID'] = $FormID;


		if($response = json_decode(self::sendRequest($fields), true)){return $response;}

		return false;
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

        self::$Token = false;
		return false;
	}

    public static function setToken()
    {
        if(self::$Token)
        {
            return '<input type="hidden" name="Token" value="' . self::$Token . '" />';
        }
        
        return false;
    }

	public static function getIt($RequestType = 'getScript', $WidthHeight = '336x280', $Debug = null)
	{
        if(self::$Token)
        {
            switch($RequestType)
            {
                case 'getImage':
			        return '<a href="http://' . self::$host . '/?RequestType=getClick&amp;Token=' . self::$Token . '" target="_blank"><img src="//' . self::$host . '/?RequestType=getImage&amp;Token=' . self::$Token . '&amp;WidthHeight=' . $WidthHeight . '&amp;PublisherID=' . self::$PublisherID . '" alt="Form protected by Keypic" /></a>';
                    break;

                default:
			        return '<script type="text/javascript" src="//' . self::$host . '/?RequestType=getScript&amp;Token=' . self::$Token . '&amp;WidthHeight=' . $WidthHeight . '&amp;PublisherID=' . self::$PublisherID . '"></script>';
                    break;
            }
        }

        return '<a href="http://keypic.com" target="_blank">At the moment Keypic is not properly configured in your system, please check if everything is configured correctly Subscribe to the service or check your FormID</a>';
	}

	// Is Spam? from 0% to 100%
	public static function isSpam($Token, $ClientEmailAddress = '', $ClientUsername = '', $ClientMessage = '', $ClientFingerprint = '')
	{
		self::$Token = $Token;

        if((self::$Token) && (self::$FormID))
        {
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

        return false;
	}

	public static function reportSpam($Token)
	{
		if($Token == ''){return 'error';}
		if(self::$FormID == ''){return 'error';}

		$fields['Token'] = $Token;
		$fields['FormID'] = self::$FormID;
		$fields['RequestType'] = 'ReportSpam';
		$fields['ResponseType'] = '2';

		if($response = json_decode(self::sendRequest($fields), true)){return $response;}
		else{return false;}
	}

	private static function sendRequest($fields)
	{
		$header = "POST " . self::$url . " HTTP/1.0\r\n";
		$header .= "Host: " . self::$host . "\r\n";
		$header .= "Content-type: application/x-www-form-urlencoded\r\n";
		$header .= self::$UserAgent . "\r\n";

        $opts = array('http' =>
            array(
                    'method'  => 'POST',
                    'header'  => $header,
                    'content' => http_build_query($fields),
                    'timeout' => 3
                )
            );

        $context  = stream_context_create($opts);
        if($result = @file_get_contents('http://' . self::$host, false, $context, -1, 40000))
        {
            return $result;
        }

        return false;
	}
}
