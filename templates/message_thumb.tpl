<div style = "border-top: 1px solid #e4e8ed;">
    <table>
        <tr>
            <td style = "width: 50px; padding: 5px;">
                <a href = "{FRIEND_ID}" style = "display: block">
                    <img src = "{DIALOG_PHOTO}" style = "width: 50px">
                </a>
            </td>
            <td>
                <div style = "width: 120px; font-size: 11px">
                    <a href = "{FRIEND_ID}">{DIALOG_NAME}</a>
                </div>
                <div style = "color: #999; font-size: 10px">
                    {DIALOG_TIME}
                </div>
            </td>
            <td>
                <a href = "{FRIEND_ID}?action=33&dialog={DIALOG_ID}">
                    <div style = "height: 50px; width: 300px; font-size: 11px; padding: 10px 5px; {STYLE}">
                        {SENDER}{MESSAGE_TEXT}
                    </div>
                </a>
            </td>
			<td>
                <a href = "{ID}?action=51&dialog={DIALOG_ID}">
					<img src = "/image/call.png">
                </a>
            </td>
        </tr>
    </table>
</div>