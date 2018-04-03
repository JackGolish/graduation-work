var constraints = {
    audio : true, 
    video : true 
};

var localStream;
var localVideo = document.getElementById('local');
var servers = null;

function successCallback(stream){
    window.stream = stream;    
    
    if(window.URL){
        localVideo.src = window.URL.createObjectURL(stream);
        trace('Received local stream');
    } 
    else {
        localVideo.src = stream;
        trace('Received local stream');
    }
    
    localStream = stream;
    
    if (navigator.webkitGetUserMedia) {
        RTCPeerConnection = webkitRTCPeerConnection;
        RTCIceCandidate = window.RTCIceCandidate;
        RTCSessionDescription = window.RTCSessionDescription;
    }
    else if(navigator.mozGetUserMedia){
        RTCPeerConnection = RTCPeerConnection;
        RTCSessionDescription = RTCSessionDescription;
        RTCIceCandidate = RTCIceCandidate;
    }
    
    start();
}

function errorCallback(error){
    console.log('navigator.getUserMedia error: ', error);
}

navigator.getUserMedia =  navigator.getUserMedia ||
                          navigator.webkitGetUserMedia ||
                          navigator.mozGetUserMedia;
                
trace('Requested local stream');

navigator.getUserMedia(constraints, successCallback, errorCallback);

var remotePC = [];

function logError(err) {
    trace(err.toString(), err);
}

function doOffer(pc, id) {
    trace('Sending offer to peer.');
    pc.createOffer(function(sessionDescription) {
        pc.setLocalDescription(sessionDescription);
        sendMessage(id, 'offer', sessionDescription);
    }, logError);
}

function doAnswer(id, pc) {
    trace('Sending answer to peer');
    pc.createAnswer(function(sessionDescription) {
        pc.setLocalDescription(sessionDescription);
        sendMessage(id, 'answer', sessionDescription);
    }, logError);
}

function sendMessage(id, type, message) {
    var xhr = new XMLHttpRequest();
    
    message = JSON.stringify(message);
    
    console.log(message);
    
    var body = 'room=' + encodeURIComponent(room) + '&receiver_id=' + encodeURIComponent(id) + '&type=' + encodeURIComponent(type) + '&message=' + encodeURIComponent(message);
    
    trace('C№->S: ' + body);
    xhr.open('POST', '/add');	
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = (function() {
    if (this.readyState !== 4) 
        return;
    });
    
    xhr.send(body);
}

function start() {
    for (var i = 0; i < number; i++) {
        var pc = new RTCPeerConnection(servers);
        pc.id = users[i];
        trace("Creating pc #" + users[i]);
        pc.onicecandidate = (function(event) {
            if (event.candidate) {
                this.addIceCandidate(new RTCIceCandidate(event.candidate));
                trace('Local ICE candidate: ' + event.candidate.candidate);
                sendMessage(this.id, 'candidate', {
                    type: 'candidate',
                    label: event.candidate.sdpMLineIndex,
                    id: event.candidate.sdpMid,
                    candidate: event.candidate.candidate
                });
            }
            else {
                trace('End of candidates');
            }
        });
        pc.onaddstream = (function(event) {            
            if (window.URL) {
                document.getElementById('remote' + this.id).src = window.URL.createObjectURL(event.stream); 
            }
            else {
                document.getElementById('remote' + this.id).src = event.stream;
            }
			
			trace('Get Remote Stream');
        });
        
        
        pc.addStream(localStream);
        if (status[i * 2] == 0) {
            doOffer(pc, users[i]);
        }
        
        remotePC.push(pc);
    }
    
	setInterval(function getPCInfo(){
		$.ajax({ 
			url: '/signaling/index?room=' + room, 
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if (obj == null) {
					trace('data is null');
					return;
				}
				var message = jQuery.parseJSON(obj.message);
				console.log(message);
				switch (message.type) {
					case 'offer': {
						trace('Got Remote Offer');
						remotePC[users.indexOf(obj.peerid)].setRemoteDescription(new RTCSessionDescription(message), function() {}, logError);
						doAnswer(obj.peerid, remotePC[users.indexOf(obj.peerid)]);
						break;
					}
					case 'answer': {
						trace('Got Remote Answer');
						remotePC[users.indexOf(obj.peerid)].setRemoteDescription(new RTCSessionDescription(message), function() {}, logError);
						break;
					}
					case 'candidate': {
						trace('Got Remote Candidate');
						remotePC[users.indexOf(obj.peerid)].addIceCandidate(new RTCIceCandidate({
							candidate: message.candidate
						}));
						break;                        
					}
				};
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
	}, 1000);
};
