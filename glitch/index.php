<html>
	<head>
		<title>Welcome to HTML</title>
			
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>
		<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js'></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css" type="text/css" />
		
		<style>
			body { background-color:#000; color:#eee;
					font-family:tahoma; }
			
			#glitch-url { width: 600px; }
			#images { position: relative; }
			.glitched { position: absolute;
						top:0;
						left:0;
						display:none; }						
		</style>
		
		<script>
			var glitchCount = 0;
			var interval = <?php if($_GET['interval']) { echo $_GET['interval']; } else { echo '3000'; } ?>;
			var autoGlitch;

		
			function superFade(element)
			{
				element.fadeIn();
				$('.glitched[count!="'+glitchCount+'"]').fadeOut(function()
				{
					$(this).remove();
				});
			}
		
			function glitch()
			{
				glitchCount++;
				$('#images').append("<img class='glitched' count='"+glitchCount+"' src='image.php?url="+$('#glitch-url').val()+"&count="+glitchCount+"' onload='superFade($(this));' />");
			}
			
			function autoGlitcher()
			{
				autoGlitch = setInterval('glitch()', interval);
				$('#glitch-me').append("<input type='button' value='Stop' id='stop' />");
			}
		
			function stop()
			{
				clearInterval(autoGlitch)
				$('#stop').remove();
			}
		
			$(document).ready(function()
			{
			
				$('#glitch-me').submit(function(event)
				{
					event.preventDefault();
					glitch();
				});			
			
				$('#auto-glitch').click(function()
				{
					autoGlitcher();
				});
				
				$('#stop').live('click', function()
				{
					stop();
				});
				
				$('#interval').click(function()
				{
					interval = prompt("How long would you like to wait? (in miliseconds)");
				
					stop();
					autoGlitcher();
				});
				
				<?php 
				
				if($_GET['url'])
					echo "glitch();";

				if($_GET['auto'])
					echo "autoGlitcher();";
					
				?>
			});
		</script>
	</head>
	
	<body>
		<h1><?php echo Glitch("What's the URL?"); ?></h1>

		<form action='image.php' id='glitch-me'>
			<input type='text' name='url' id='glitch-url' value='<?php echo $_GET['url']; ?>' />
			<input type='submit' value='Glitch' />
			<input type='button' value='Auto Glitch' id='auto-glitch' />
			<input type='button' value='Set Interval' id='interval' />		
		</form>

		<div id='images'>
			<div id='glitched'></div>
		</div>
	</body>
</html>

<?php

function Glitch($What)
{
	# Get string length
	# Do what mirc does
	
	return $What;	
}

?>
