<?php
header('Content-Type: application/json');
ini_set('date.timezone','Asia/Shanghai');
define ( 'APP_DIR', dirname ( __FILE__ ) );
define ( 'DS', DIRECTORY_SEPARATOR );
require_once APP_DIR.DS.'apiLib'.DS.'common.php';

$c = $_REQUEST ['c'];
if(!empty($c)){

	switch ($c){
		case 'index':
			include 'apiAction/index.php';
			break;
		case 'user':
			include 'apiAction/user.php';
			break;
		case 'publicEvent':
			include 'apiAction/public_event.php';
			break;
		case 'shop':
			include 'apiAction/shop.php';
			break;
		case 'userEvent':
			include 'apiAction/user_event.php';
			break;
		case 'contact':
			include 'apiAction/contact.php';
			break;
		case 'chat':
			include 'apiAction/chat.php';
			break;
		case 'feedback':
			include 'apiAction/feedback.php';
			break;
		default:
			break;
	}

}
?>