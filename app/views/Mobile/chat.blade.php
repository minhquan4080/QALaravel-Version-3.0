<!DOCTYPE html>
<!--[if lt IE 7]>	  <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>		 <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>		 <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="public/css/bootstrap.css" type="text/css" media="screen">
	<link rel="stylesheet" href="public/css/bootstrap-responsive.css" type="text/css" media="screen">
	<script src="public/js/modernizr-2.6.2.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12" id="chat-app">
				<h1>
					Hello, {{Sentry::getUser()->username}}
				</h1>
				<form class="form-inline">
					<input type="text" class="input" id="chat-message" placeholder="Type a message and hit enter.">
				</form>
                <div id="chat-log">

                </div>
			</div>
		</div>
	</div>

	<!--@scripts start-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
	<script src="public/js/bootstrap.js"></script>
	<script src="public/js/brain-socket.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(function(){
			//var fake_user_id = Math.floor((Math.random()*1000)+1);
			var fake_user_id = '{{Sentry::getUser()->username}}';
			//var fake_user_name = {{Sentry::getUser()->username}};
			//make sure to update the port number if your ws server is running on a different one.
			window.app = {};
			app.BrainSocket = new BrainSocket(
					new WebSocket('ws://localhost:8080'),
					new BrainSocketPubSub()
			);
			app.BrainSocket.Event.listen('generic.event',function(msg){
				console.log(msg);

				if(msg.client.data.user_id == fake_user_id){
					$('#chat-log').append('<div class="alert alert-success">{{Sentry::getUser()->username}}: '+msg.client.data.message+'</div>');
				}else{
					$('#chat-log').append('<div class="alert alert-info">'+msg.client.data.user_id+': '+msg.client.data.message+'</div>');
				}
			});
			app.BrainSocket.Event.listen('app.success',function(data){
				console.log('An app success message was sent from the ws server!');
				console.log(data);
			});
			app.BrainSocket.Event.listen('app.error',function(data){
				console.log('An app error message was sent from the ws server!');
				console.log(data);
			});
			$('#chat-message').keypress(function(event) {
						if(event.keyCode == 13){
							app.BrainSocket.message('generic.event',
									{
										'message':$(this).val(),
										'user_id':fake_user_id
									}
							);
							$(this).val('');
						}
						return event.keyCode != 13; }
			);
		});
	</script>
</body>
</html>