<div id = "new_message">
    <div style = "padding: 10px 10px 11px; background: #597BA5; position: relative; overflow: hidden">
        <a style = "float: right; padding: 17px 26px 18px; margin: -10px -10px -11px; color: #C7D7E9;" onclick = "removeElement('new_message');">
            Закрыть
        </a>
        <div style = "position: absolute; width: 32px; height: 32px; margin-top: -1px">
            <a href = "/socialnetwork.com/index.php?id={FRIEND_ID}">
                <img src = "{FRIEND_PHOTO}" style = "width: 32px">
            </a>
        </div>
        <div style = "color: #FFF; padding: 7px 16px; margin-left: 26px; font-weight: bold; font-size: 11px">
            {FRIEND_NAME} прислал вам сообщение
        </div>
    </div>
    <div style = "width: 400px; min-height: 89px; pref-height: 104px; background-color: #F7F7F7; border: 1px solid #e4e8ed; padding: 10px; overflow: hidden">
        {MESSAGE}
    </div>
</div>