<td style = "vertical-align: top; padding-top: 10px;">
    <div style = "float: left; width: 140px; font-size: 12px; line-height: 140%; padding: 0 0 0 5px">
        <ol style = "list-style: none; padding: 0">
            <li>{MY_PAGE}</li>
            <li>{MY_FRIENDS}</li>
            <li>{MY_MESSAGES}</li>
            <li>{MY_DOCUMENTS}</li>
        </ol>
    </div>
    <div style = "float: right">
        <div style = "margin: 10px 0; height: 10px;">
            <ul style = "list-style: none; width: 605px; margin: 0; padding: 0">
                <li style = "float: left; margin-right: 25px">
                    <a href = "{ID}?action=31">Все диалоги</a>
                </li>
                <li style = "float: right; margin-right: 25px; color: #666">
                    Создать конференцию
                </li>
            </ul>
        </div> 
        <div style = "padding: 8px 73px 70px">
            <form method = "POST" action = "{ID}?action=35">
                <h3 style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 0 0px 9px; font-size: 1.09em; font-family: Arial;">
                    Назовите конференцию
                </h3>
                <input class = "form-control" type = "text" name = "name" style = "width: 300px;" placeholder = "Название конференции" required>
                <h3 style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 24px 0px 9px; font-size: 1.09em; font-family: Arial">
                    Добавьте друзей в конференцию<img id = "question" src = "/image/question.png" style = "margin-left: 5px;" data-tooltip = "CTRL + MOUSE для выбора<br>нескольких друзей">
                </h3>
                <select class = "form-control" required style = "width: 300px; display: block" size = "10" multiple name = "members[]">
                    {FRIENDS}
                </select>
                <h3 style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 24px 0px 9px; font-size: 1.09em; font-family: Arial">
                    Сообщение:
                </h3>
                <textarea class = "form-control" name = "message" style = "display: block; width: 300px; resize: none; margin-bottom: 15px" required></textarea>
                <input class = "btn btn-default" type = "submit" value = "Создать конференцию">
            </form>
        </div>       
    </div>
</td>