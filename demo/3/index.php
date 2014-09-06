<?php

/*
* This is the Keypic class you must include
*/
include_once('../Keypic.php');

ini_set('display_errors', 'on'); 
error_reporting(E_ALL & ~E_NOTICE);

$email = $_REQUEST['email'];
$password = $_REQUEST['password'];
$Token = $_REQUEST['Token'];

/*
 *	instead of xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx insert your FormID
 *  get registered here -> https://keypic.com/?action=register
 *	get your FormID here -> https://keypic.com/?action=forms
 *	IMPORTANT FormID must be secret, don't share it
*/

Keypic::setFormID('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
//Keypic::setDebug(true);

if($_SERVER['REQUEST_METHOD'] ==  "POST")
{
	if($username!=='' && $email!=='' && $message!=='')
	{
		if($spam = Keypic::isSpam($Token, $email, $username, $message))
		{
		    if(is_numeric($spam))
		    {
                if($spam < 39) $color = "green";
                elseif($spam > 69) $color = "red";

			    echo '<font color="' . $color . '"> This message has ' . $spam . '% of spam probability</font><br />';
			    echo Keypic::getIt('getScript') . '<br />';
			    echo '<a href="">reload</a>';
			    die();
		    }
		    else
		    {
			    echo '<font color="red"> There was an error: '. $spam .'</font><br />';
			    die();
		    }
		}
		else
		{
		        echo '<font color="red">It was not possible to determine spam probability</font><br />';
		        echo '<a href="">reload</a>';
		        die();
		}


	}
	else{$error = '<font color="red">Complete all the fields</font><br />';}
}

?>
<!DOCTYPE html> 
<html> 
 <head>
 <style>
 html,body{margin:0; padding:0; font-family:Verdena,Tahoma,sans-serif;}
 body{background:#FFF;color:#333}
 a img{border:0;}
 div.greybox{text-align: center; background-color: #f7f7f7; border: solid #cccccc; border-width: 1px 1px 1px 1px; color: #333333; padding: 10px; font-size: 13px; -webkit-border-radius: 5px; -moz-border-radius: 5px;}
 </style>
 </head>
 <body dir="ltr">
<b><a href="/demo/">Keypic PHP Live Demo home</a></b>
<div class="greybox">
<form method="post" action="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/demo/3/">

Email: <br />
<input type="text" name="email" value="<?php echo $email;  ?>" /> <br />
Password: <br />
<input type="password" name="password" value="<?php echo $password;  ?>" /> <br />
<input type="hidden" name="Token" value="<?php echo Keypic::getToken($Token, $email); ?>" /> <br />
<?php echo Keypic::getIt('getScript'); ?> <br />
<input type="submit" value="Send"> <br />
</form>
<?php echo $error; ?>
</div>
 </body>
</html>