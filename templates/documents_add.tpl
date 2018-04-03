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
                <li style = "float: right; margin-right: 25px; color: #666">
                    Добавить документ
                </li>
            </ul>
        </div> 
        <div style = "padding: 8px 73px 70px; border-top: 1px solid #e4e8ed;">
            <form enctype = "multipart/form-data" action = "{ID}?action=43" method = "POST">
                <input type = "hidden" name = "MAX_FILE_SIZE" value = "3146000">
                <h3 style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 0 0px 9px; font-size: 1.09em; font-family: Arial;">
                    Выберите файл:
                </h3>
                <input class = "btn btn-default" type = "file" name = "upfile" style = "width: 300px; margin-bottom: 15px;" required>
                <h3 style = "color: #45688E; line-height: 1.27em; margin: 0px; padding: 0 0px 9px; font-size: 1.09em; font-family: Arial;">
                    Максимальный размер файла: 3Mb
                </h3>
                <input class = "btn btn-default" type = "submit" value = "Загрузить"> 
            </form>
        </div>       
    </div>
</td>