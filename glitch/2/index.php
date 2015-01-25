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

		</style>
		
		<script>
			var glitchCount = 0;
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
				
				$.get("base64.php", {'url': 'http://a3.l3-images.myspacecdn.com/images01/21/6fb81069d69374c5088e769965ed3f7f/l.gif'}, function(data)
				{
					$('#glitch-image').attr('src', data);
					$('#glitch-data').val(data);
				});				
			}
			
			function autoGlitch()
			{
				autoGlitch = setInterval('glitch()', 3000);
				$('#glitch-me').append("<input type='button' value='Stop' id='stop' />");
			}
		
			function stop()
			{
				clearInterval(autoGlitch)
				$(this).remove();
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
					autoGlitch();
				});
				
				$('#stop').live('click', function()
				{
					stop();
				});
				
				$('#text-glitch').click(function()
				{
					$('#glitch-image').attr('src', $('#glitch-data').val());					
				});
				
				<?php 
				
				if($_GET['url'])
					echo "glitch();";

				if($_GET['auto'])
					echo "autoGlitch();";
					
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
		</form>

	
		<img id='glitch-image'>
		<textarea id='glitch-data'></textarea>
		<input type='button' value='Text Glitch' id='text-glitch' />

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
