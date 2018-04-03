<div id = "dark" class = "hide" style = "left: 0; top: 0; position: absolute; background: url(/image/dark.png); z-index: 2; width: 100%; height: 100%;">
    <div style = "width: 450px; height: 350px; margin: 100px auto 0; color: #fff; background-color: #fff">
        <div style = "padding: 10px 10px 11px; background: #597BA5; position: relative; overflow: hidden">
            <a style = "float: right; padding: 17px 26px 18px; margin: -10px -10px -11px; color: #C7D7E9;" onclick = "HideMessages('dark');">
                Закрыть
            </a>
            <div style = "position: absolute; width: 32px; height: 32px; margin-top: -1px">
                <a href = "{FRIEND_ID}">
                    <img id = "user_img" src = "{FRIEND_PHOTO}" style = "width: 32px">
                </a>
            </div>
            <div style = "color: #FFF; padding: 7px 16px; margin-left: 26px; font-weight: bold; font-size: 1.09em">
                Новое сообщение
            </div>
        </div>
        <div style = "padding: 26px; background: #F7F7F7; height: 250px">
            <div style = "color: #45688E; line-height: 1.27em; margin: 0px; padding-bottom: 9px; font-size: 1.09em; font-weight: bold;">
                Получатель
            </div>
            <div id = "receiver" style = "color: #45688E; line-height: 1.27em; margin: 0px; font-size: 1.09em; font-weight: bold;">
                {FRIEND_NAME}
            </div>
            <div style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 26px 0px 9px; font-size: 1.09em; font-weight: bold;">
                Сообщение
            </div>
            <form id = "message_form" method = "POST" action = "{FRIEND_ID}?action=32">
                <div class = "form-group" style = "margin-bottom: 10px;">
                    <textarea class = "form-control" style = "resize: none; width: 90%; height: 100px" name = "text" placeholder = "Введите сообщение" required></textarea>
                </div>
                <input class = "btn btn-default" type = "submit" value = "Отправить сообщение">
            </form>
        </div>
    </div>
</div>