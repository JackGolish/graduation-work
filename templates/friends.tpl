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
                    <a href = "{ID}?action=21" style = "text-decoration: none; color: #666">Все друзья</a>
                </li>
                <li style = "float: left; margin-right: 25px">
                    <a href = "{ID}?action=22" style = "text-decoration: none; color: #666">Заявки в друзья</a>
                </li>
            </ul>
        </div> 
        <div style = "padding: 10px 0; border-top: 1px solid #e4e8ed">
            <span>У вас {FRIENDS_NUMBER}</span>
        </div>
        <div>
            {FRIENDS_THUMBS}
        </div>       
    </div>
</td>