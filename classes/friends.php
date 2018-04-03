<?php
class friends {
    private $query;
    
    function friend_invite($id, $friend_id, $link) {
		$this->query = "SELECT user_id, friend_id FROM les_friends WHERE user_id IN ($id, $friend_id) AND friend_id IN ($id, $friend_id)";
		$result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
		
		if (!empty($result)) {
			return;
		}
		
        $this->query = "INSERT INTO les_friends (id, user_id, friend_id, status) VALUES (NULL, $friend_id, $id, 0)";
        mysqli_query($link, $this->query);
    }
    
    function friend_delete($id, $friend_id, $link) {
        $this->query = "DELETE FROM les_friends WHERE user_id IN($id, $friend_id) AND friend_id IN($id, $friend_id)";
        mysqli_query($link, $this->query);
    }
    
    function invitation_list($id, $link, $tpl) {
        $helptpl = new template();
        $this->query = "SELECT friend_id FROM les_friends WHERE user_id = $id AND status = 0";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC);
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/friends.tpl');
        $this->count_invitations($id, $link, $tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
		$tpl->set_value('ID', $id);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
        $html = "";
        
        foreach ($result as $array) {
            foreach ($array as $value) {              
				$helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/friend_thumb.tpl');
				$this->query = "SELECT first_name, last_name FROM les_user WHERE id = $value";
				$temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
				$helptpl->set_value('FRIEND_NAME', $temp['first_name'] . ' ' . $temp['last_name']);
				$helptpl->set_value('FRIEND_PHOTO', '/image/default.jpg');
				$helptpl->set_value('ACTION1', 'href="' . $value . '?action=24"');
				$helptpl->set_value('ACTION_FIRST_MSG', 'Принять заявку в друзья');
				$helptpl->set_value('ACTION2_ID', '25');
				$helptpl->set_value('ACTION_SECOND_MSG', 'Отклонить заявку в друзья');
				$helptpl->set_value('FRIEND_ID', $value);
				
				if ($this->check_online($value, $link, $tpl)) {
					$helptpl->set_value('ONLINE', 'online');
					$helptpl->set_value('ONLINE_STYLE', '#0c0');
				}
				else {
					$helptpl->set_value('ONLINE', 'offline');
					$helptpl->set_value('ONLINE_STYLE', '#f00');
				}
				
                $helptpl->template_parse();
                $html = $html . $helptpl->html;
            }
        }  
        
        unset($helptpl);
        $tpl->set_value('FRIENDS_THUMBS', $html);
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
    }
    
    private function count_invitations($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as 'count' FROM les_friends WHERE status = 0 AND user_id = $id";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        $tpl->set_value('FRIENDS_NUMBER', $result['count'] . ' заявок');
    }
    
    private function load_documents($id, $tpl) {
        $tpl->set_value('MY_DOCUMENTS', '<a href = "/user/' . $id . '?action=41">Мои Документы</a>');
    }
    
	private function check_online($id, $link, $tpl) {
		date_default_timezone_set("Europe/Kiev");
        $this->query = "SELECT online FROM les_user WHERE id = $id";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));

		if (time() - strtotime($result['online']) < 5) {
			return true;
		}
        
		else {
			return false;
		}
    }
	
    function friends_list($id, $link, $tpl) {
        $helptpl = new template();
        $this->query = "SELECT user_id FROM les_friends WHERE friend_id = $id AND status = 1"
                . " UNION SELECT friend_id FROM les_friends WHERE user_id = $id AND status = 1";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query));
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.templates', '/') . '/message_box.tpl');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/friends.tpl');
        $this->count_friends($id, $link, $tpl);
		$tpl->set_value('ID', $id);		
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
        $html = "";
        
        foreach ($result as $array) {
            foreach ($array as $value) {              
                $helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/friend_thumb.tpl');
                $this->query = "SELECT first_name, last_name FROM les_user WHERE id = $value";
                $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
                $friend_name = $temp['first_name'] . ' ' . $temp['last_name'];
                $helptpl->set_value('FRIEND_NAME', $friend_name);
                $helptpl->set_value('FRIEND_PHOTO', '/image/default.jpg');
                $helptpl->set_value('ACTION1', "onclick = \"fillAttributes($value, '/image/default.jpg', '$friend_name');\"");
                $helptpl->set_value('ACTION_FIRST_MSG', 'Написать сообщение');
                $helptpl->set_value('ACTION2_ID', '23');
                $helptpl->set_value('ACTION_SECOND_MSG', 'Удалить из друзей');
                $helptpl->set_value('FRIEND_ID', $value);
			   	if ($this->check_online($value, $link, $tpl)) {
					$helptpl->set_value('ONLINE', 'online');
					$helptpl->set_value('ONLINE_STYLE', '#0c0');
				}
				else {
					$helptpl->set_value('ONLINE', 'offline');
					$helptpl->set_value('ONLINE_STYLE', '#f00');
				}
                $helptpl->template_parse();
                $html = $html . $helptpl->html;
            }
        }  
        
        unset($helptpl);
        $tpl->set_value('FRIENDS_THUMBS', $html);
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') .'/footer.html');
    }
    
    function accept_invite($id, $friend_id, $link, $tpl) {
        $this->query = "UPDATE les_friends SET status = 1 WHERE user_id = $id AND friend_id = $friend_id";
        mysqli_query($link, $this->query);
        $this->friends_list($id, $link, $tpl);
    }
    
    function decline_invite($id, $friend_id, $link, $tpl) {
        $this->query = "DELETE FROM les_friends WHERE user_id = $id AND friend_id = $friend_id";
        mysqli_query($link, $this->query);
        $this->friends_list($id, $link, $tpl);
    }
    
    private function load_friends($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as 'count' FROM les_friends WHERE user_id = $id AND status = 0";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
        if ($result['count'] > 0) {
            $tpl->set_value('MY_FRIENDS', '<a href = "/user/' . $id . '?action=21">Мои Друзья *NEW*</a>');
        }
        else {
            $tpl->set_value('MY_FRIENDS', '<a href = "/user/' . $id . '?action=21">Мои Друзья</a>');
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
            $tpl->set_value('MY_MESSAGES', '<a href = "/user/' . $id . '?action=31">Мои Сообщения *NEW*</a>');
        }
        else {
            $tpl->set_value('MY_MESSAGES', '<a href = "/user/' . $id . '?action=31">Мои Сообщения</a>');
        }
    }
    
    private function count_friends($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as 'count' FROM les_friends WHERE (user_id = $id OR friend_id = $id) AND status = 1";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        $tpl->set_value('FRIENDS_NUMBER', $result['count'] . ' друзей');
    }
}

$friendsobj = new friends();
?>