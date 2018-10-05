<?php

ini_set('display_errors', 1);
require 'config.php';
$database = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);
$welcome= "Hello new user! Welcome to this bot! Reply this message with your Ether wallet address to continue.

If you have already registered your wallet, Kindly ignore this message and continue, Thanks!";


if ($text == '/start')
 {      
    $reply_markup = $telegram->buildForceReply(false);
    $content = array(
    'chat_id' => $chat_id,
    'text' =>  "$welcome",
    "parse_mode" => "Html",
    "disable_web_page_preview" => "true",
    "reply_markup" => $reply_markup
    );
    $telegram->sendMessage($content);
 }

 else if ($text == '/reset')
 {      
    $reply_markup = $telegram->buildForceReply(false);
    $content = array(
    'chat_id' => $chat_id,
    'text' =>  "Reply me with your updated ether wallet address ( Please note that it will be added as it is, So just send the address and nothing else )",
    "parse_mode" => "Html",
    "disable_web_page_preview" => "true",
    "reply_markup" => $reply_markup
    );
    $telegram->sendMessage($content);
 }

else if(strstr($data['message']['reply_to_message']['text'],"Reply me with your updated ether wallet address")!=false)
{
    $text  = trim($text," ");
    $query = "update user set ether_address= '$text' where chat_id = '$chat_id'";
    echo $query;
    if($database->query($query))
    {
     $content = array(
        'chat_id' => $chat_id,
        'text' =>  "You wallet address has been updated to :<code>$text</code>",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true",
        );
        $telegram->sendMessage($content);
    }  
}

else if(isset($data['message']['forward_from']))
{   
    if($chat_id!=' ')
    return;

    $id            = $data['message']['forward_from']['id'];
    $query         = "select * from user where chat_id ='$id'";
    $result        = $database->query($query);
    echo $query;
    $result        = mysqli_fetch_array($result);
    $user_id       = $result['chat_id'];
    $username      = $result['username'];
    $ether_address = $result['ether_address'];
    $balance       = $result['balance'];

    $option = array( 
        array($telegram->buildInlineKeyBoardButton( "Update balance", '' , $callback_data = "Update balance $id" )));
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' =>  "Here are the user details :

Chat Id : <code>$user_id</code>
Username : <code>$username</code>
Ether address : <code>$ether_address</code>
Balance: <code>$balance</code>",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true"
            );
        $telegram->sendMessage($content);
}

else if (strstr($data['message']['reply_to_message']['text'],"the new balance")!=false)
{   
    $explode = explode(" ",$data['message']['reply_to_message']['text']);
    $id      = $explode[5];
    $text    = trim($text," ");
    $sql     = "Update user set balance='$text' where chat_id = '$id'";
    $result  = $database->query($sql);

    if($result== true)
    {
        $reply_markup = $telegram->buildForceReply(false);
        $content = array(
        'chat_id' => $chat_id,
        'text' =>  "Updation successfull",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true",
        "reply_markup" => $reply_markup
        );
        $telegram->sendMessage($content);
    }
    else 
    {
        $content = array(
        'chat_id' => $chat_id,
        'text' =>  "Some error ",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true",
        );
        $telegram->sendMessage($content);
    }    
 }



 if($text=='/send')
 {
    $option = array( 
        array($telegram->buildInlineKeyBoardButton( "Channel 1 Forward ", '' , $callback_data = "channel_1" )), 
        array($telegram->buildInlineKeyBoardButton( "Channel_2 Forward ", '' , $callback_data = "channel_2" )), 
        array($telegram->buildInlineKeyBoardButton( "Your Ether wallet",'' , $callback_data = "Ether" )));
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
    'chat_id' => $chat_id,
    'reply_markup' => $keyb,
    'text' =>  'Select one of the give options',
    "parse_mode" => "Html",
    "disable_web_page_preview" => "true"
        );
    $telegram->sendMessage($content);
 }

 if($type=='callback_query')
 {   
     $data = $data['callback_query'];
     $callback_data = $data['data'];

     if($callback_data=='channel_1')
     {
      $reply_markup = $telegram->buildForceReply(false);     
      $content = array(
        'chat_id' => $chat_id,
        'text' =>  "Forward to channel_1",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true",
        "reply_markup" => $reply_markup
        );
     }
     else if($callback_data=='channel_2')
     {
        $reply_markup = $telegram->buildForceReply(false);
        $content = array(
            'chat_id' => $chat_id,
            'text' =>  "Forward to channel_2",
            "parse_mode" => "Html",
            "disable_web_page_preview" => "true",
            "reply_markup" => $reply_markup
            );

     }
     else if(strstr($callback_data,'Update balance')!=false)
     {   
        $explode      = explode(" ",$callback_data);
        $id           = $explode[2];
        $reply_markup = $telegram->buildForceReply(false);

        $content = array(
            'chat_id' => $chat_id,
            'text' =>  "Send the new balance for $id",
            "parse_mode" => "Html",
            "disable_web_page_preview" => "true",
            "reply_markup" => $reply_markup
            );
    }
    else 
     { 
        $chat_id = $data['from']['id'];
        $query   = "select ether_address from user where chat_id ='$chat_id'";

        $ether_address = $database->query($query);
        $ether_address = mysqli_fetch_array($ether_address);
        $ether_address = $ether_address['ether_address'];

        $content = array(
            'chat_id' => $chat_id,
            'text' =>  "Your Ether : <code>$ether_address</code>",
            "parse_mode" => "Html",
            "disable_web_page_preview" => "true"
            );
     }
    $telegram->sendMessage($content);
    return;
 }

 else if(array_key_exists('reply_to_message',$data['message']))
 {
   if(strstr($data['message']['reply_to_message']['text'],"Forward to channel_1")!=false)
   {
       $message_id = $data['message']['message_id'];
       $content = array(
           'chat_id'=>"-1001263750140",
           'from_chat_id'=>"$chat_id",
           'message_id' => "$message_id"
       );
       $telegram->forwardMessage($content);
   }
   else if(strstr($data['message']['reply_to_message']['text'],"Forward to channel_2")!=false)
   {
       $message_id = $data['message']['message_id'];
       $content = array(
           'chat_id'=>"-1001286407383",
           'from_chat_id'=>"$chat_id",
           'message_id' => "$message_id"
       );
       $telegram->forwardMessage($content);
       return;
   }
   
else if(strstr($data['message']['reply_to_message']['text'],"Reply this message with your Ether wallet address to continue")!=false)
{
   $username = "@".$data['message']['from']['username'];
   $text     = trim($text," ");
   $query    = "insert into user values ('$chat_id','$username','$text','0')";
   $result   = $database->query($query);
   $content;

   if($result == true)
   {
    $content = array(
    'chat_id' => $chat_id,
    'text' =>  "Registration successfull!
Here are you details:

Username: $username
Ether address : <code>$text</code>

Wrong info? Register again with correct info, We will update it !",
    "parse_mode" => "Html",
    "disable_web_page_preview" => "true"
    );
   }
   else 
   {
    $content = array(
        'chat_id' => $chat_id,
        'text' =>  "Some error, Please contact my master",
        "parse_mode" => "Html",
        "disable_web_page_preview" => "true"
        );
    }
    $telegram->sendMessage($content);
    return;
    } 
}

?>

