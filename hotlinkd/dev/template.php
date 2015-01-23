<html>
	<head>
		<title>$Caption &mdash; hotlinked on wetfish.net</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<style>
			body { margin:0; background:#000; color: #fff; font-family: Tahoma,Helvetica,Sans-serif; font-size:11pt; margin-left:4%; margin-right:4%; }
			
			a { color:#E87EE1; text-decoration: none; }
			a:hover { text-decoration: underline; }
			a.title { color:#FFF; font-weight:bold; }
			
			div.superborder
			{	background-image:url('http://click.wetfish.net/super.gif');
				font-size:16pt;
				font-weight:bold;
				margin-bottom:8px;
				border-bottom: 1px solid #000;
				border-right: 1px solid #000;
				border-left: 1px solid #000;
				-moz-border-radius-bottomleft:8px;
				-moz-border-radius-bottomright:8px;
				-webkit-border-bottom-left-radius:8px;
				-webkit-border-bottom-right-radius:8px; }
		

			div.supernav
			{	background-image:url('http://click.wetfish.net/background.png'); 
				padding:8px;

				border-bottom: 1px solid #98b3cd;
				border-right: 1px solid #98b3cd;
				border-left: 1px solid #98b3cd;
				-moz-border-radius-bottomleft:8px;
				-moz-border-radius-bottomright:8px;
				-webkit-border-bottom-left-radius:8px;
				-webkit-border-bottom-right-radius:8px; }
		
			div.body { margin-left:3%; margin-right:3%; }
			
			.navigation { float:right; }
			.extra { float:right; }
			
			.tags { margin:8px 0px; }
			.tag { padding:0px 8px; }
			.content img { max-width:100% }
			.time { width:125px; }
			
			.strike { text-decoration:line-through; }
			.big { font-size: 16pt; font-weight: bold; }
			.medium { font-size: 13pt; font-weight: bold; }
			.small { font-size: 8pt; }
			.warning { background:yellow; color:black; font-weight:bold; padding:0px 4px 0px 4px; }
			.error { background:red; color:black; font-weight:bold; padding:0px 4px 0px 4px; }
		</style>
		
		<link rel="icon" type="image/png" href="http://wiki.wetfish.net/favzz.png"/>
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>
		<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js'></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" type="text/css" />
	</head>
	
	<body>
		<div class='superborder'>
			<div class='supernav'>
				<a href='http://wetfish.net'>The Wetfish $Blog</a> |
				<a href='http://wiki.wetfish.net'>The Wetfish Wiki</a> |
				<a href='http://click.wetfish.net'>Clickfish</a> |
				<a href='http://mirror.wetfish.net'>Mirrorfish</a> |
				<a href='http://glitch.wetfish.net'>Glitchfish</a>
			</div>
		</div>
		
		<div class='body'>
			<div class='navigation'>
				<a href='/'>Home</a> |
				<a href='/Search'>Search</a> |
				<a href='/Recent'>Recent Images</a>
			</div>
		
			<h1><?php echo $Friend->Poop; ?></h1>
			
			<div class='content'>
				<?php echo $Friend->Butt; ?>
			</div>
			
			<div class='extra'>
				$Extra
			</div>
			
			<div class='tags'>
				$Tags
			</div>
			
			<center><iframe id='leader-friend' src='http://wetfish.net/friendship/leader.html' style='width:750px; height:115px; border:0; outline:0; overflow:hidden;' scrolling="no"></iframe></center>
		</div>
	</body>
</html>
