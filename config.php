<?php

    $bot_token = "553882868:AAH_mkzC-MbvlJz8iy9OzMigPoZkUaZUbAM";
    // Using the TelegramBotPHP library by Eleirbag89 - https://github.com/Eleirbag89/TelegramBotPHP
    require ("Telegram.php");



	$telegram   = new Telegram($bot_token);
	$website    ='https://api.telegram.org/bot'."$bot_token";
	$text       = $telegram->Text();
    $data       = $telegram->getData();
	$chat_id    = $telegram->ChatID();
	$type       = $telegram->getUpdateType();	
	$dbhost     = "localhost";  
	$dbname     = "channel_forward";
	$dbusername = "root";
	$dbpassword = "root";


?>
