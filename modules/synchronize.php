<?php
session_start();
$id = $_SESSION['id'];
session_write_close();
include '../connection_configuration.php';
include 'template.php';
set_time_limit(1);
$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
mysqli_set_charset($link, 'utf8');

$online = "UPDATE users SET online = NOW() WHERE id = $id";

mysqli_query($link, $online);

$query = "SELECT id, text, sender_id FROM messages "
        . "WHERE dialog_id IN (SELECT dialog_id FROM dialog_members "
        . "WHERE friend_id IN (SELECT id FROM friends "
        . "WHERE user_id = $id OR friend_id = $id)) "
        . "AND status = 0 AND sender_id <> $id";
$result = mysqli_fetch_assoc(mysqli_query($link, $query));

while (true) {
    if (!empty($result)) {                      
        $message_id = $result['id'];
        $sender_id = $result['sender_id'];
        $temp_query = "SELECT firstname, surname FROM users WHERE id = $sender_id";
        $temp_result = mysqli_fetch_assoc(mysqli_query($link, $temp_query));

        $tpl->get_template('../templates/received_message.tpl');
        $tpl->set_value("FRIEND_NAME", $temp_result['firstname'] . ' ' . $temp_result['surname']);
        $tpl->set_value("FRIEND_ID", $sender_id);
        $tpl->set_value("FRIEND_PHOTO", 'images/default.jpg');
        $tpl->set_value("MESSAGE", $result['text']);
        $tpl->template_parse();

        $json = json_encode($tpl->html);
        echo $json;

        $temp_query = "UPDATE messages SET status = -1 WHERE id  = $message_id";
        mysqli_query($link, $temp_query);
        
        break;
    } 
    
    else {
        sleep(1);
        continue;
    }
}
?>