<?php

if($_GET['url'])
{
	$URL = parse_url($_GET['url']);
	
	if($URL['host'])
	{
		$file = pathinfo($URL['path']);
		
		if(in_array(strtolower($file['extension']), array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'tif', 'tiff')))
		{
			$location = tempnam('/tmp', 'Glitch_');
			$data = @file_get_contents($_GET['url']);
			file_put_contents($location, $data);
	
			$type = mime_content_type($location);
			$base64 = base64_encode($data);
			
			echo "data:$type;base64,$base64";
		}
	}
}

?>