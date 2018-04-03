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
	<div id = "container" style = "width: 610px;">
            <div id = "video">
                {REMOTES}
                <video id = "local" autoplay muted></video>
            </div>			
            <div id = "sidebar">
                <button id = "hangUp" class = "btn btn-default">Hang Up</button>
            </div>
	</div>
    </div>
    {SCRIPT}
</td>