<?php 
class calls {
	private $query;
	
	private function load_friends($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as 'count' FROM les_friends WHERE user_id = $id AND status = 0";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
        if ($result['count'] > 0) {
            $tpl->set_value('MY_FRIENDS', '<a href = "' . $id . '?action=21">Мои Друзья *NEW*</a>');
        }
        else {
            $tpl->set_value('MY_FRIENDS', '<a href = "' . $id . '?action=21">Мои Друзья</a>');
        }
    }
    
    private function load_messages($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as count FROM les_dialogs "
                . "INNER JOIN les_dialog_members ON (les_dialogs.id = les_dialog_members.dialog_id) "
                . "INNER JOIN les_messages ON (les_dialogs.last_msg_id = les_messages.id) "
                . "WHERE les_dialog_members.friend_id IN ("
                . "SELECT id FROM les_friends WHERE les_friends.user_id = $id OR les_friends.friend_id = $id) "
                . "AND les_messages.status < 1 AND les_messages.sender_id <> $id";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
        if ($result['count'] > 0) {
            $tpl->set_value('MY_MESSAGES', '<a href = "' . $id . '?action=31">Мои Сообщения *NEW*</a>');
        }
        else {
            $tpl->set_value('MY_MESSAGES', '<a href = "' . $id . '?action=31">Мои Сообщения</a>');
        }
    }
	
	private function load_documents($id, $tpl) {
        $tpl->set_value('MY_DOCUMENTS', '<a href = "' . $id . '?action=41">Мои Документы</a>');
    }
	
	function initialize($user_id, $dialog_id, $link, $tpl) {
		$this->query = "SELECT user_id, friend_id FROM les_friends WHERE id IN(SELECT friend_id FROM les_dialog_members WHERE dialog_id = $dialog_id)";
		$users = array();
		$status = array();

		$result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC);
		foreach ($result as $array) {
			if ($array['user_id'] == $user_id) {
				array_push($users, $array['friend_id']);
				array_push($status, "0");
				
			}
			else {
				array_push($users, $array['user_id']);
				array_push($status, "0");
			}
		}

		$this->query = "SELECT name FROM les_dialogs WHERE id = $dialog_id AND name IS NOT NULL "
				. "UNION "
				. "SELECT concat(les_user.first_name, ' ', les_user.last_name) as name "
				. "FROM les_user, les_dialogs "
				. "WHERE les_user.id = $user_id AND les_dialogs.name IS NULL";

		$result = mysqli_fetch_assoc(mysqli_query($link, $this->query));

		$this->query = "INSERT INTO les_rooms(id, name) VALUES(NULL, \"" . $result['name'] . "\"); "
				. "SELECT id FROM les_rooms "
				. "ORDER BY id DESC LIMIT 1";

		mysqli_multi_query($link, $this->query);
		$link->next_result();
		$temp_query = mysqli_fetch_assoc($link->store_result());

		$this->query = "INSERT INTO les_calls(id, room_id, member_id, status) "
				. "VALUES(NULL, " . $temp_query['id'] . ", $user_id, 1); ";
		foreach ($users as $value) {
			$this->query = $this->query . "INSERT INTO les_calls(id, room_id, member_id, status) "
					. "VALUES(NULL, " . $temp_query['id'] . ", $value, 0); ";
		}

		mysqli_multi_query($link, $this->query);
		while ($link->next_result()) {
			$link->store_result();
		}

		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');

		$tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/call.tpl');
		$this->load_documents($user_id, $tpl);
        $this->load_friends($user_id, $link, $tpl);
        $this->load_messages($user_id, $link, $tpl);
		$html = "";

		foreach ($users as $value) {
			$html = $html . "<video id = \"remote" . $value . "\" autoplay></video>";
		}

		$tpl->set_value('REMOTES', $html);
		$tpl->set_value('MY_PAGE', '<a href = "' . $user_id . '">Моя Страница</a>');
		$script = "
		<script>
			var room = " . $temp_query['id'] . ";
			var users = " . json_encode($users) . ";
			var remoteStreams = [];
			var number = " . count($users) . ";
			var status = " . json_encode($status) . ";
			for (var i = 0; i < number; i++) {
				remoteStreams.push(document.getElementById(\"remote\" + users[i]));
			}
		</script>
		<script src = \"/script/main.js\">
		</script>
		";

		$tpl->set_value('SCRIPT', $script);

		$tpl->template_parse();
		echo $tpl->html;

		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
	}
	
	function appendToRoom($user_id, $room_id, $link, $tpl) {
		$this->query = "UPDATE les_calls SET status = 1 WHERE room_id = $room_id AND member_id = $user_id;";
		mysqli_query($link, $this->query);

		$users = array();
		$status = array();

		$this->query = "SELECT member_id as 'member', status FROM les_calls "
				. "WHERE room_id = $room_id AND member_id <> $user_id";

		$result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC);
		
		foreach ($result as $array) {
			array_push($users, $array['member']);
			if ($array['status'] == 1) {
				array_push($status, $array['status']);
			}
			else {
				array_push($status, '0');
			}
		} 

		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');

		$tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/call.tpl');
		$this->load_documents($user_id, $tpl);
        $this->load_friends($user_id, $link, $tpl);
        $this->load_messages($user_id, $link, $tpl);
		$html = "";

		foreach ($users as $value) {
			$html = $html . "<video id = \"remote" . $value . "\" autoplay></video>";
		}

		$tpl->set_value('REMOTES', $html);
		$tpl->set_value('MY_PAGE', '<a href = "' . $user_id . '">Моя Страница</a>');
		$script = "
		<script>
			var room = " . $room_id . ";
			var users = " . json_encode($users) . ";
			var remoteStreams = [];
			var number = " . count($users) . ";
			var status = " . json_encode($status) . ";
			console.log(status);

			for (var i = 0; i < number; i++) {
				remoteStreams.push(document.getElementById(\"remote\" + users[i]));
			}
		</script>
		<script src = \"/script/main.js\">
		</script>
		";

		$tpl->set_value('SCRIPT', $script);

		$tpl->template_parse();
		echo $tpl->html;

		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
	}
}

$callobj = new calls();
?>