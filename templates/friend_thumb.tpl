<div style = "padding: 10px 5px; height: 100px; border-top: 1px solid #e4e8ed;">
    <div style = "float: left;">
        <a href = "{FRIEND_ID}" style = "display: block;">
            <img src = '{FRIEND_PHOTO}' style = "width: 80px">
        </a>
    </div>
    <span style = "margin-left: 10px; color: #666; font-weight: bold;">
        <a href = "{FRIEND_ID}">
            {FRIEND_NAME}
        </a>
    </span>
	<br>
	<span style = "margin-left: 10px; color: {ONLINE_STYLE}">
		{ONLINE}
	</span>
    <div style = "float: right; padding-right: 150px;">
        <a {ACTION1}>
            {ACTION_FIRST_MSG}
        </a>
        <br />
        <a href = "{FRIEND_ID}?action={ACTION2_ID}">
            {ACTION_SECOND_MSG}
        </a>
    </div>
</div>