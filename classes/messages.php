<?php
class messages {
    private $query;
    
    function show_conference_constructor($id, $link, $tpl) {
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/conference.tpl');
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);
		$tpl->set_value('ID', $id);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя страница</a>');
        $tpl->template_parse();
        
        $this->query = "SELECT user_id FROM les_friends WHERE friend_id = $id "
                . "UNION "
                . "SELECT friend_id FROM les_friends WHERE user_id = $id";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query));
        $html = '';
        
        foreach ($result as $value) {
            $this->query = "SELECT first_name, last_name FROM les_user WHERE id = $value[0]";
            $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $friend_name = $temp['first_name'] . ' ' . $temp['last_name'];
            $html .= "<option value = \"$value[0]\">$friend_name</option>";
        }
        
        $tpl->set_value('FRIENDS', $html);
        $tpl->template_parse();
        echo $tpl->html;
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');        
    }
    
    function create_conference($id, $dialog_name, $message, $members, $link, $tpl) {
		$this->query = "SELECT * FROM les_dialogs WHERE name = '$dialog_name'";
		if (!empty(mysqli_fetch_all(mysqli_query($link, $this->query)))) {
			echo 'Диалог с данным именем уже существует';
			$this->show_dialogs($id, $link, $tpl);
			return;
		}
		
        $this->query = "INSERT INTO les_dialogs(id, last_msg_id, name) VALUES(NULL, NULL, '$dialog_name'); "
                . " SELECT id FROM les_dialogs ORDER BY id DESC LIMIT 1";
        
        mysqli_multi_query($link, $this->query);
        $link->next_result();
        $result = mysqli_fetch_assoc($link->store_result());
        $dialog_id = $result['id'];  
        
        foreach ($members as $value) {
            $this->query = "SELECT id FROM les_friends WHERE user_id IN($id, $value) AND friend_id IN($id, $value)";
            $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $friend_id = $temp['id'];
            $this->query = "INSERT INTO les_dialog_members(id, dialog_id, friend_id) VALUES (NULL, $dialog_id, $friend_id)";
            mysqli_query($link, $this->query);
            $link->store_result();
        }
        
        $this->query = "INSERT INTO les_messages(dialog_id, status, time, text, sender_id) "
                . "VALUES($dialog_id, 0, CURRENT_TIMESTAMP, '$message', $id); "
                . "UPDATE les_dialogs SET last_msg_id = (SELECT id FROM les_messages ORDER BY id DESC LIMIT 1) WHERE id = $dialog_id";       
        mysqli_multi_query($link, $this->query);
        
        while ($link->next_result()) {
            $link->store_result();
        }

        $this->show_dialogs($id, $link, $tpl);    
    }
    
    function send_conf_message($id, $dialog_id, $link, $text, $tpl) {
        $this->query = "INSERT INTO les_messages(dialog_id, status, text, sender_id) "
                . "VALUES ('$dialog_id', '0', '$text', '$id'); "
                . "UPDATE les_dialogs SET last_msg_id = ("
                . "SELECT LAST_INSERT_ID()) WHERE id = $dialog_id";
        mysqli_multi_query($link, $this->query);
        
        while ($link->next_result()) {
            $link->store_result();
        }
        
        $this->show_dialogs($id, $link, $tpl);
    }
    
    private function load_documents($id, $tpl) {
        $tpl->set_value('MY_DOCUMENTS', '<a href = "' . $id . '?action=41">Мои Документы</a>');
    }
    
    function send_message($id, $friend_id, $link, $text, $tpl) {
        $this->query = "SELECT dialog_id FROM ("
                . "SELECT COUNT(id), dialog_id, friend_id FROM les_dialog_members "
                . "GROUP BY dialog_id HAVING COUNT(id) < 2) AS temp_dialog "
                . "WHERE friend_id = (SELECT id FROM les_friends "
                . "WHERE user_id IN($id, $friend_id) AND friend_id IN($id, $friend_id) AND status = 1)";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC);       
        
        if (!empty($result)) {
            foreach ($result as $array) {
                $dialog_id = $array['dialog_id'];
                $this->query = "INSERT INTO les_messages(dialog_id, status, text, sender_id) "
                        . "VALUES ('$dialog_id', '0', '$text', '$id'); "
                        . "UPDATE les_dialogs SET last_msg_id = ("
                        . "SELECT LAST_INSERT_ID()) WHERE id = $dialog_id";
                mysqli_multi_query($link, $this->query);
            }
        }   
        else {
            $this->query = "INSERT INTO les_dialogs(last_msg_id, name) "
                    . "VALUES (NULL, NULL); "
                    . "SELECT LAST_INSERT_ID() AS last_id";
            mysqli_multi_query($link, $this->query);
            
            $link->next_result();
            $result = mysqli_fetch_assoc($link->store_result());  

            $dialog_id = $result['last_id'];
            $this->query = "SELECT id FROM les_friends WHERE user_id IN($id, $friend_id) "
                    . "AND friend_id IN($id, $friend_id)";
            $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
  
            $friends = $result['id'];
            $this->query = "INSERT INTO les_dialog_members(dialog_id, friend_id) VALUES($dialog_id, $friends)";
            mysqli_query($link, $this->query);
            $this->query = "INSERT INTO les_messages(dialog_id, status, text, sender_id) "
                        . "VALUES ('$dialog_id', '0', '$text', '$id'); "
                        . "UPDATE les_dialogs SET last_msg_id = ("
                        . "SELECT id FROM les_messages ORDER BY id DESC LIMIT 1) WHERE id = $dialog_id";
            mysqli_multi_query($link, $this->query);
        }
        
        while ($link->next_result()) {
            $link->store_result();
        }
        
        $this->show_dialogs($id, $link, $tpl);
    }
    
    function show_private_dialog($id, $dialog_id, $link, $tpl) {
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');

        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/dialogs.tpl');
        $this->query = "SELECT name FROM les_dialogs WHERE id = $dialog_id";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
		
        if ($result['name'] == NULL) {
			$this->query = "SELECT les_friends.friend_id as 'friend_id', les_friends.user_id as 'user_id' "
                    . "FROM les_dialog_members "
                    . "INNER JOIN les_dialogs ON (les_dialog_members.dialog_id = les_dialogs.id) "
                    . "INNER JOIN les_friends ON (les_dialog_members.friend_id = les_friends.id) "
                    . "WHERE les_dialogs.name IS NULL AND les_dialogs.id = $dialog_id;";
            $temp_res = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            
			if ($id == $temp_res['user_id']) {
                $friend_id = $temp_res['friend_id'];
            }
            else {
                $friend_id = $temp_res['user_id'];
            }
			
            $this->query = "SELECT last_name, first_name FROM les_user WHERE id = $friend_id";
            $temp_query = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $result['name'] = $temp_query['first_name'] . ' ' . $temp_query['last_name'];
        }
		else {
            $friend_id = $id;
        }        
        
        $tpl->set_value('FRIEND_NAME', $result['name']);
        $tpl->set_value('DIALOG_ID', $dialog_id);
		$tpl->set_value('ID', $id);
        $tpl->set_value('FRIEND_PHOTO', '/image/default.jpg');
        $tpl->set_value('FRIEND_ID', $friend_id);
        $tpl->set_value('USER_PHOTO', '/image/default.jpg');
        $this->count_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя страница</a>');
        $html = "";
        
        $this->query = "SELECT text, status, time, sender_id FROM les_messages "
                . "WHERE dialog_id = $dialog_id "
                . "ORDER BY time ASC";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC); 

        $helptpl = new template();
        
        foreach ($result as $array) { 
			$helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/dialog_thumb.tpl');
            $sender_id = $array['sender_id'];
            $this->query = "SELECT first_name, last_name FROM les_user WHERE id = $sender_id";
            $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $sender_name = $temp['first_name'] . ' ' . $temp['last_name'];
            $helptpl->set_value('SENDER_NAME', $sender_name);
            $helptpl->set_value('SENDER_ID', $sender_id);
            $helptpl->set_value('SENDER_PHOTO', '/image/default.jpg');
            $helptpl->set_value('MESSAGE', $array['text']);
            $helptpl->set_value('DATE', $array['time']);
            
            if ($array['status'] < 1) {
                $helptpl->set_value('STYLE', 'background-color: #EDF1F5');
            }
            else {
                $helptpl->set_value('STYLE', '');
            }
            
            $helptpl->template_parse();
            $html = $html . $helptpl->html;
        }  
        
        unset($helptpl);
        $tpl->set_value('DIALOG_THUMBS', $html);
        
        
        $tpl->template_parse();
        echo $tpl->html;
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
      
        $this->query = "UPDATE les_messages SET status = 1 WHERE sender_id <> $id "
                . "AND dialog_id = $dialog_id";
        mysqli_query($link, $this->query);
    }
    
    private function count_friends($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as 'count' FROM les_friends WHERE (user_id = $id OR friend_id = $id) AND status = 1";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        $tpl->set_value('FRIENDS_NUMBER', $result['count']);
    }
    
    function count_dialogs($id, $link, $tpl) {
        $this->query = "SELECT COUNT(*) as count FROM ("
                . "SELECT DISTINCT dialog_id FROM les_dialog_members "
                . "WHERE friend_id IN (SELECT id FROM les_friends "
                . "WHERE user_id = $id OR friend_id = $id)) as temp";
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
        $tpl->set_value('DIALOGS_NUMBER', $result['count']);        
    }
    
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
    
    function show_dialogs($id, $link, $tpl) {
        $helptpl = new template();
        $this->query = "SELECT DISTINCT les_dialogs.id as 'dialog_id', les_messages.text as 'text'"
                . ", les_messages.time as 'time', les_messages.status, les_messages.sender_id, "
                . "les_dialogs.name as 'dialog_name' FROM les_dialogs "
                . "INNER JOIN les_dialog_members ON (les_dialogs.id = les_dialog_members.dialog_id) "
                . "INNER JOIN les_messages ON (les_dialogs.last_msg_id = les_messages.id) "
                . "WHERE les_dialog_members.friend_id IN ("
                . "SELECT id FROM les_friends WHERE les_friends.user_id = $id OR les_friends.friend_id = $id) "
                . "ORDER BY les_messages.time DESC";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query), MYSQLI_ASSOC);
                
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/messages.tpl');
        $this->count_dialogs($id, $link, $tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);
		$tpl->set_value('ID', $id);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя страница</a>');
        $html = "";        
        
        foreach ($result as $array) {  
            $helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/message_thumb.tpl');
            $dialog_id = $array['dialog_id'];

            if ($array['sender_id'] == $id) {
                $helptpl->set_value('SENDER', '<img src = "/image/default.jpg" style = "width: 30px; float: left; margin-right: 10px">');
            }
            else {
                $helptpl->set_value('SENDER', '');
            }

            if ($array['dialog_name'] == NULL) { 
                $this->query = "SELECT user_id, friend_id FROM les_friends WHERE id = ("
                        . "SELECT friend_id FROM les_dialog_members WHERE dialog_id = $dialog_id)";
                $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));

                if ($temp['user_id'] == $id) {
                    $query_id = $temp['friend_id'];
                }
                else {
                    $query_id = $temp['user_id'];
                }
				
                $this->query = "SELECT first_name, last_name FROM les_user WHERE id = $query_id";
                $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
                $array['dialog_name'] = $temp['first_name'] . ' ' . $temp['last_name'];
            }
            else {
                $query_id = $id;
            }

            $helptpl->set_value('DIALOG_NAME', $array['dialog_name']);
            $helptpl->set_value('DIALOG_PHOTO', '/image/default.jpg');
            $helptpl->set_value('DIALOG_TIME', $array['time']);
            $helptpl->set_value('DIALOG_ID', $dialog_id);
			$helptpl->set_value('ID', $id);
            $helptpl->set_value('FRIEND_ID', $query_id);
            $helptpl->set_value('MESSAGE_TEXT', $array['text']);

            if ($array['status'] < 1) {
                $helptpl->set_value('STYLE', 'background-color: #F7F7F7');
            }
            else {
                $helptpl->set_value('STYLE', '');
            }

            $helptpl->template_parse();
            $html = $html . $helptpl->html;
        }  
        
        unset($helptpl);
        $tpl->set_value('MESSAGES_THUMBS', $html);
        $tpl->template_parse();
        echo $tpl->html;
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
    }
}

$messagesobj = new messages();       
?>