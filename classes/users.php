<?php
class users {
	//переменная, содержащая в себе строку запроса
    private $query;
    //функция загрузки "моего" профиля
    private function load_my_page($id, $link, $tpl) {
		//выбираем данные о пользователе
        $this->query = "SELECT * FROM les_user WHERE id = \"$id\"";
		//установка у соединения кодировки utf8 для верного отображения русских букв
        mysqli_set_charset($link, 'utf8');
		//преобразуем результат в ассоциативный массив
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
		//выводим содержимое файла head.html по адресу /views/user/html/head.html
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        //загружаем шаблон main_page
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates') . '/main_page.tpl');
		$this->load_name($result['first_name'] . ' ' . $result['last_name'], $tpl);
        $this->load_photo($tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);	
		$tpl->set_value('ONLINE', 'online');
		$tpl->set_value('ONLINE_STYLE', '#0c0');
		//заполняем массив значений {OLD_VALUE => NEW_VALUE}
        $tpl->set_value('ACTION', 'Изменить фотографию');
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
		//меняем в шаблоне {OLD_VALUE} на NEW_VALUE
        $tpl->template_parse();
		//выводим сгенерированный html код
        echo $tpl->html;
        //выводим footer
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
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

    private function load_name($name, $tpl) {
        $tpl->set_value('USER', $name);
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
    
    private function load_photo($tpl) {
        $tpl->set_value('MAIN_PHOTO', '/image/default.jpg');
    }
    
    private function load_friend_page($id, $friend_id, $link, $tpl) {
        $this->query = "SELECT * FROM les_user WHERE id = \"$friend_id\"";
        mysqli_set_charset($link, 'utf8');
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        
        $helptpl = new template();
        $helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/message_box.tpl');
        $helptpl->set_value('FRIEND_ID', $friend_id);
        $helptpl->set_value('FRIEND_NAME', $result['first_name'] . ' ' . $result['last_name'], $tpl);
        $helptpl->set_value('FRIEND_PHOTO', '/image/default.jpg');
        $helptpl->template_parse();
        echo $helptpl->html;
        
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/main_page.tpl');
        $this->load_name($result['first_name'] . ' ' . $result['last_name'], $tpl);
        $this->load_photo($tpl);
		
		if ($this->check_online($friend_id, $link, $tpl)) {
			$tpl->set_value('ONLINE', 'online');
			$tpl->set_value('ONLINE_STYLE', '#0c0');
		}
		else {
			$tpl->set_value('ONLINE', 'offline');
			$tpl->set_value('ONLINE_STYLE', '#f00');
		}
		
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('ACTION', '<button class = "btn btn-default" onclick = "getVisible(\'dark\');">Написать сообщение</button>');
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
    }
    
    private function load_user_page($id, $friend_id, $link, $tpl) {
        $this->query = "SELECT first_name, last_name FROM les_user WHERE id = \"$friend_id\"";
        mysqli_set_charset($link, 'utf8');
        $result = mysqli_fetch_assoc(mysqli_query($link, $this->query));
        
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');

        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates') . '/main_page.tpl');
        $this->load_name($result['first_name'] . ' ' . $result['last_name'], $tpl);
        $this->load_photo($tpl);
		
		if ($this->check_online($friend_id, $link, $tpl)) {
			$tpl->set_value('ONLINE', 'online');
			$tpl->set_value('ONLINE_STYLE', '#0c0');
		}
		else {
			$tpl->set_value('ONLINE', 'offline');
			$tpl->set_value('ONLINE_STYLE', '#f00');
		}
		
        $this->load_friends($id, $link, $tpl);
        $this->load_documents($id, $tpl);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя страница</a>');
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
    }
    
    public function load_page($id, $friend_id, $link, $tpl) {
        $this->tpl = $tpl;
        
        if ($id == $friend_id) {
            $this->load_my_page($id, $link, $tpl);
        }
        else {
            $query = "SELECT * FROM les_friends WHERE user_id IN($id, $friend_id) AND friend_id IN($id, $friend_id)";
            $result = mysqli_fetch_assoc(mysqli_query($link, $query));
            if (!isset($result['status'])) {
                $tpl->set_value('ACTION', '<a href = "' . $friend_id. '?action=20">Добавить в друзья</a>');
                $this->load_user_page($id, $friend_id, $link, $tpl);
            }
            else {
                switch ($result['status']) {
                    case -1:
                        $tpl->set_value('ACTION', 'Пользователь отклонил вашу заявку в друзья');
                        $this->load_user_page($id, $friend_id, $link, $tpl);
                        break;
                    case 0:
                        $tpl->set_value('ACTION', 'Ожидание подтверждения запроса на добавление');
                        $this->load_user_page($id, $friend_id, $link, $tpl);
                        break;
                    case 1:
                        $this->load_friend_page($id, $friend_id, $link, $tpl);
                        break;
                }
            }
        }
    }
}

$userobj = new users();
?>