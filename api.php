<?php
//содержит переменные для подключения к бд
include 'connection_configuration.php';
//подключаем шаблонизатор
include_once 'modules/template.php';
//подключаем классы реализации
include_once 'classes/users.php';
include_once 'classes/friends.php';
include_once 'classes/messages.php';
include_once 'classes/documents.php';
include_once 'classes/calls.php';
//устанавливаем соединение с базой данных
$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
//записываем в user_id идентификатор пользователя (рабочий аналог $_SESSION['id'])
$user_id = Yii::app()->user->id;
//В случае, если нам передали ещё один идентификатор, заносим его в переменную
if ($model['id'] != $user_id) {
	$friend_id = $model['id'];
}  
//проверка на ошибки
if (!$link) {
    printf("Невозможно подключиться к серверу баз данных. Код ошибки: %s\n", mysqli_connect_error());
    die();
}
//установка кодировки utf8 у соединения
mysqli_set_charset($link, 'utf8');
//если нам передали action, то переходим по пунктам, пока не найдём свой
if (isset($_GET['action'])) {
	//примечания к функциям см. в классах или документации
    switch ($_GET['action']) {
        case 20: 
            $friendsobj->friend_invite($user_id, $friend_id, $link);
			$userobj->load_page($user_id, $friend_id, $link, $tpl);
			//$tpl - объект шаблонизатора
            break;
        case 21:
            $friendsobj->friends_list($user_id, $link, $tpl);
            break;
        case 22:
            $friendsobj->invitation_list($user_id, $link, $tpl);
            break;
        case 23: 
            $friendsobj->friend_delete($user_id, $friend_id, $link);
            $friendsobj->friends_list($user_id, $link, $tpl);
            break;
        case 24:
            $friendsobj->accept_invite($user_id, $friend_id, $link, $tpl);
            break;
        case 25:
            $friendsobj->decline_invite($user_id, $friend_id, $link, $tpl);
            break;
        case 31:
            $messagesobj->show_dialogs($user_id, $link, $tpl);
            break;
        case 32:
            $messagesobj->send_message($user_id, $friend_id, $link, mysqli_real_escape_string($link, $_POST['text']), $tpl);
            break;
        case 33:
            $messagesobj->show_private_dialog($user_id, $_GET['dialog'], $link, $tpl);
            break;
        case 34:
            $messagesobj->show_conference_constructor($user_id, $link, $tpl);
            break;
        case 35:
            $messagesobj->create_conference($user_id, mysqli_real_escape_string($link, $_POST['name']), mysqli_real_escape_string($link, $_POST['message']), $_POST['members'], $link, $tpl);
            break;
        case 36:
            $messagesobj->send_conf_message($user_id, $_GET['dialog'], $link, mysqli_real_escape_string($link, $_POST['text']), $tpl);
            break;
        case 41:
            $documentobj->show_documents($user_id, $link, $tpl);
            break;
        case 42:
            $documentobj->add_document($user_id, $link, $tpl);
            break;
        case 43:
            $documentobj->upload_document($user_id, $link, $tpl);
            break;
        case 44: 
            $documentobj->show_shared_constructor($user_id, $link, $tpl);
            break;
        case 45:
            $documentobj->share_document();
            break;
		case 51:
			$callobj->initialize($user_id, $_GET['dialog'],/*dialog_id*/ $link, $tpl);
			break;
		case 52:
			$callobj->appendToRoom($user_id, $_GET['dialog'],/*room_id*/ $link, $tpl);
			break;
        default: break;
    }
}
else {
	//если action не указан, выводим профиль пользователя
	if (!isset($friend_id)) {
		$userobj->load_page($user_id, $user_id, $link, $tpl);    
	}
	else {
		$userobj->load_page($user_id, $friend_id, $link, $tpl);
	}
}
//закрываем соединение с базой
mysqli_close($link);
?>