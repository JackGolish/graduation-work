<?php
class documents {
    private $query;
    //Функция отображения конструктора для предоставления доступа к документу
    function show_shared_constructor($id, $link, $tpl) {
        echo file_get_contents('html/head.html');
        echo file_get_contents('html/header.html');
        
        $tpl->get_template('templates/share.tpl');
        $this->load_documents($tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_messages($id, $link, $tpl);
        $tpl->set_value('MY_PAGE', '<a href = /socialnetwork.com>Моя страница</a>');
        $this->query = "SELECT user_id FROM friends WHERE friend_id = $id "
                . "UNION "
                . "SELECT friend_id FROM friends WHERE user_id = $id";
        $result = mysqli_fetch_all(mysqli_query($link, $this->query));
        $html = '';
        
        foreach ($result as $value) {
            $this->query = "SELECT firstname, surname FROM users WHERE id = $value[0]";
            $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $friend_name = $temp['firstname'] . ' ' . $temp['surname'];
            $html .= "<option value = \"$value[0]\">$friend_name</option>";
        }
        
        $tpl->set_value('FRIENDS', $html);
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents('html/footer.html');
    }
    
    function show_documents($id, $link, $tpl) {
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/documents.tpl');
        $this->load_documents($id, $tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_messages($id, $link, $tpl);
		$tpl->set_value('ID', $id);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
        $this->query = "SELECT link, name FROM les_documents WHERE owner_id = $id";
        $result = mysqli_query($link, $this->query);
        
        if (!empty($result)) {
            $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $html = "";
            $helptpl = new template();
                    
            foreach ($result as $array) {
                $helptpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/document_thumb.tpl');
                $file_link = $array['link'];
                $name = $array['name'];
                $helptpl->set_value('DOCUMENT_LINK', $file_link);
                $helptpl->set_value('DOCUMENT_NAME', $name);
                $helptpl->template_parse();
                $html = $html . $helptpl->html;
            }
            
            $this->query = "SELECT COUNT(*) as 'count' FROM les_documents WHERE owner_id = $id";
            $temp = mysqli_fetch_assoc(mysqli_query($link, $this->query));
            $tpl->set_value('DOCUMENTS_NUMBER', $temp['count']);           
            $tpl->set_value('DOCUMENTS_THUMBS', $html);
        }
        else {
            $tpl->set_value('DOCUMENTS_NUMBER', 'нет');
            $tpl->set_value('DOCUMENTS_THUMBS', '');
        }
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
    }
    
    function upload_document($id, $link, $tpl) {
        $dir = 'uploads/user' . $id . '/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        if (isset($_FILES["upfile"])) {
            $upfile = $_FILES["upfile"]["tmp_name"];
            $error_code = $_FILES["upfile"]["error"];  
            $upfile_name = $_FILES["upfile"]["name"];
                        
            if ($error_code == 0) {
                $unique_name = uniqid() . $upfile_name;
                $path = $dir . $unique_name;               
                              
                $this->query = "INSERT INTO les_documents(link, owner_id, name) "
                        . "VALUES('$path', $id, '$upfile_name')";
                mysqli_query($link, $this->query);
                
                $upfile_name = mb_convert_encoding($unique_name, 'windows-1251', 'utf-8');
                $dir = $dir . $upfile_name;
                move_uploaded_file($upfile, $dir);
            }
        }
        
        $this->show_documents($id, $link, $tpl);
    }
    
    function add_document($id, $link, $tpl) {
		echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/head.html');
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/header.html');
        
        $tpl->get_template(Yii::getPathOfAlias('application.views.user.templates', '/') . '/documents_add.tpl');
        $this->load_documents($id, $tpl);
        $this->load_friends($id, $link, $tpl);
        $this->load_messages($id, $link, $tpl);
		$tpl->set_value('ID', $id);
        $tpl->set_value('MY_PAGE', '<a href = "' . $id . '">Моя Страница</a>');
        $tpl->template_parse();
        echo $tpl->html;
        
        echo file_get_contents(Yii::getPathOfAlias('application.views.user.html', '/') . '/footer.html');
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
}

$documentobj = new documents();
?>