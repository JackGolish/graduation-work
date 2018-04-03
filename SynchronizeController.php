<?php
class SynchronizeController extends CController {
	public function filters() {
        return array(
            'ajaxOnly + index',
        );
    }
	
	function actionIndex(){
		$id = Yii::app()->user->id;
		require_once 'additional/connection_configuration.php';
		require_once 'additional/template.php';
		$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
		mysqli_set_charset($link, 'utf8');

		$online = "UPDATE les_user SET online = NOW() WHERE id = $id";

		mysqli_query($link, $online);

		$query = "SELECT id, text, sender_id FROM les_messages "
				. "WHERE dialog_id IN (SELECT dialog_id FROM les_dialog_members "
				. "WHERE friend_id IN (SELECT id FROM les_friends "
				. "WHERE user_id = $id OR friend_id = $id)) "
				. "AND status = 0 AND sender_id <> $id";
		$result = mysqli_fetch_assoc(mysqli_query($link, $query));
		
		$second_query = "SELECT les_rooms.name as 'name', les_rooms.id as 'id' FROM les_rooms "
        . "INNER JOIN les_calls ON(les_rooms.id = les_calls.room_id) "
        . "WHERE les_calls.member_id = $id AND les_calls.status = 0";
		$next_result = mysqli_fetch_assoc(mysqli_query($link, $second_query));
		
		if (!empty($result)) {                      
			$message_id = $result['id'];
			$sender_id = $result['sender_id'];
			$temp_query = "SELECT first_name, last_name FROM les_user WHERE id = $sender_id";
			$temp_result = mysqli_fetch_assoc(mysqli_query($link, $temp_query));
			
			$tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/received_message.tpl');
			$tpl->set_value("FRIEND_NAME", $temp_result['first_name'] . ' ' . $temp_result['last_name']);
			$tpl->set_value("FRIEND_ID", $sender_id);
			$tpl->set_value("FRIEND_PHOTO", '/image/default.jpg');
			$tpl->set_value("MESSAGE", $result['text']);
			$tpl->template_parse();
			$data["type"] = "message";
			$data["data"] = $tpl->html;
			
			$json = json_encode($data);
			echo $json;

			$temp_query = "UPDATE les_messages SET status = -1 WHERE id  = $message_id";
			mysqli_query($link, $temp_query);
		} 
			
		if (!empty($next_result)) {
			$tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/new_call.tpl');
			$tpl->set_value('ID', $id);
			$tpl->set_value('CALLER_NAME', $next_result['name']);
			$tpl->set_value('ROOM_ID', $next_result['id']);
			$tpl->template_parse();
			$data["type"] = "call";
			$data["data"] = $tpl->html;
			$json = json_encode($data);
			echo $json;
			
			$room_id = $next_result['id'];
			$temp_query = "UPDATE les_calls SET status = -1 WHERE room_id = $room_id AND member_id = $id;";
			mysqli_query($link, $temp_query);
		}			
	}
}
?>