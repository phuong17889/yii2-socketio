<?php
/* @var $this \yii\web\View */
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Starter</h5>
        </div>
        <div class="card-body">
            This demo will show how <b>Room</b> work.<br>
            Run the command <code>php yii broadcast/room</code> or
            <button type="button" data-event="receiver" class="btn btn-broadcast btn-sm btn-primary">click here
            </button>
        </div>
    </div>
    <div class="card mt-3 border-primary">
        <div class="card-header">
            <h5 class="card-title">Request</h5>
        </div>
        <div class="card-body">
            This example has no send any request to server
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
	var socket = io('localhost:1369/publisher');
	socket.on('update_on_publisher', function (data) {
		console.log(data);
		$('.socketio-response').append(JSON.stringify(data) + '\n');
	});
</script>
