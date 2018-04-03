function getVisible(id) {
    var elem = document.getElementById(id);
    elem.removeAttribute('class');
    elem.setAttribute('class', 'show');
}

function removeElement(id) {
    var parent = document.getElementById(id).parentNode;
    parent.removeChild(document.getElementById(id));
}

function HideMessages(id) {
    var elem = document.getElementById(id);
    elem.removeAttribute('class');
    elem.setAttribute('class', 'hide');
}

function fillAttributes(id, source, name) {
    var img = document.getElementById('user_img');
    img.setAttribute('src', source);
    img.parentNode.setAttribute('href', id);
    document.getElementById('message_form').setAttribute('action', id + '?action=32');
    document.getElementById('receiver').innerHTML = name;
    getVisible('dark');
}

var showingTooltip;

document.onmouseover = function(e) {
    var target = e.target;

    var tooltip = target.getAttribute('data-tooltip');
    if (!tooltip) {
        return;
    }

    var tooltipElem = document.createElement('div');
    tooltipElem.id = 'tooltip';
    tooltipElem.innerHTML = tooltip;
    document.body.appendChild(tooltipElem);

    var coords = target.getBoundingClientRect();

    var left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
    if (left < 0) {
        left = 0; // не вылезать за левую границу окна
    }

    var top = coords.top - tooltipElem.offsetHeight - 5;
    if (top < 0) { // не вылезать за верхнюю границу окна
        top = coords.top + target.offsetHeight + 5;
    }

    tooltipElem.style.left = left + 'px';
    tooltipElem.style.top = top + 'px';

    showingTooltip = tooltipElem;
};

document.onmouseout = function(e) {
    if (showingTooltip) {
        document.body.removeChild(showingTooltip);
        showingTooltip = null;
    }
};

$(function() {
    $('<audio id="chatAudio" src = "/files/notify.mp3"></audio>').appendTo('body');
});

setInterval(function poll(){
    $.ajax({ 
        url: '/synchronize', 
        success: function(data) {
            var obj = jQuery.parseJSON(data);
			if (obj == null) 
				return;
			console.log(obj);
			
            if (obj['type'] === 'message') {
                $('#chatAudio')[0].play();
                $(obj['data']).appendTo('body');
            }
            if (obj['type'] === 'call') {
                $(obj['data']).appendTo('body');
                $('#modal-1').attr('style', 'display: block');
            }
        }, 
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
    });
}, 2000);

