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
                    <a href = "{ID}?action=42">
                        Добавить документ
                    </a>
                </li>
            </ul>
        </div> 
        <div style = "padding: 10px 0; border-top: 1px solid #e4e8ed;">
            <span>У вас {DOCUMENTS_NUMBER} документов</span>
        </div>
        <div style = "padding: 10px 0; border-top: 1px solid #e4e8ed;">
            {DOCUMENTS_THUMBS}
        </div>       
    </div>
</td>