<?php
class SignalingController extends CController {
	public function filters() {
        return array(
            'ajaxOnly + index',
        );
    }
	function actionIndex($room){
		$id = Yii::app()->user->id;
		$room_id = $room;
		require_once 'additional/connection_configuration.php';
		$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
		mysqli_set_charset($link, 'utf8');

		$query = "SELECT id, sender_id, type, message "
				. "FROM les_call_meta "
				. "WHERE receiver_id = $id "
				. "AND room_id = $room_id "
				. "ORDER BY sender_id ASC "
				. "LIMIT 1";
		$result = mysqli_fetch_assoc(mysqli_query($link, $query));

		if (!empty($result)) { 
			$call_meta_id = $result['id'];
			
			$data['peerid'] = $result['sender_id'];
			$data['message'] = $result['message'];
			
			$json = json_encode($data);
			echo $json;

			$temp_query = "DELETE FROM les_call_meta WHERE id = $call_meta_id";
			mysqli_query($link, $temp_query);
		}
	}
}
?>