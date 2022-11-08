<?php
/* @var $this \yii\web\View */
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Starter</h5>
        </div>
        <div class="card-body">
            This demo will show how <b>Receiver</b> work.<br>
            Run the command <code>php yii broadcast/receiver</code> or
            <button type="button" data-event="receiver" class="btn btn-broadcast btn-sm btn-primary">click here
            </button>
        </div>
    </div>
    <div class="card mt-3 border-primary">
        <div class="card-header">
            <h5 class="card-title">Request</h5>
        </div>
        <div class="card-body">
            <form class="form-inline">
                <div class="form-group">
                    <label for="message"></label>
                    <input type="text" name="message" id="message" class="form-control" placeholder="Type some message">
                </div>
                <div class="form-group mt-2">
                    <button type="button" class="btn btn-success btn-sm btn-send">Send</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-3 border-warning">
        <div class="card-header">
            <h5 class="card-title">Response</h5>
        </div>
        <div class="card-body">
            <pre class="socketio-response"></pre>
        </div>
    </div>

</div>
<script>
	var socket = io('localhost:1369/receiver');
	socket.on('response', function (data) {
		console.log(data);
		$('.socketio-response').append(JSON.stringify(data) + '\n');
	});
	$(document).on('click', '.btn-send', function () {
		let message = $('#message');
		socket.emit('request', {message: message.val()});
		message.val('');
	});
</script>
