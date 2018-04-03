<?php
class AddController extends CController {
	/*public function filters() {
        return array(
            'ajaxOnly + index',
        );
    }*/
	
	function actionIndex(){
		$user_id = Yii::app()->user->id;
		require_once 'additional/connection_configuration.php';

		$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

		if (isset($_POST['message'])) {
			$type = $_POST['type'];
			$room = $_POST['room'];
			$message = $_POST['message'];
			$receiver = $_POST['receiver_id'];
			$message = mysqli_real_escape_string($link, $message);
			
			$query = "INSERT INTO les_call_meta(id, room_id, sender_id, receiver_id, type, message) "
					. "VALUES(NULL, $room, $user_id, $receiver, '$type', '$message')";

			mysqli_query($link, $query);
		}
	}
}
?>