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
        <div style = "margin: 10px 0; height: 10px">
            <ul style = "list-style: none; width: 605px; margin: 0; padding: 0">
                <li style = "float: left; margin-right: 25px">
                    <a href = "{ID}?action=31">Все диалоги</a>
                </li>
                <li>
                    <span style = "color: #2B587A; font-weight: bold">
                        {FRIEND_NAME}
                    </span>
                </li>
            </ul>
        </div> 
        <table cellpadding = "0" cellspacing = "0" width = "620" style = "border-top: 1px solid #DAE1E8; margin-top: 10px;">
            <tbody>
                {DIALOG_THUMBS}
            </tbody>
        </table>
        <div style = "background-color: #F2F2F2;">
            <div style = "border: 1px solid #DAE1E8; padding: 8px 15px 0 67px;">
                <table>
                    <tbody>
                        <tr>
                            <td style = "width: 50px; padding-right: 10px; vertical-align: top;">
                                <img src = "{USER_PHOTO}" height = "50" width = "50">
                            </td>
                            <td>
                                <form method = "POST" action = "{ID}?action=36&dialog={DIALOG_ID}">
                                    <textarea name = "text" style = "width: 358px; height: 59px; resize: none; margin-bottom: 5px; display: block;"></textarea>
                                    <input class = "btn btn-default" type = "submit" value = "Отправить">
                                </form>
                            </td>
                            <td style = "width: 163px; padding-left: 10px; vertical-align: top;">
                                <div>
                                    <a href = "{FRIEND_ID}">
                                        <img src = "{FRIEND_PHOTO}" width = "50" height = "50">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>     
    </div>						
</td>