<?php
session_start();

$RandomStr = md5(microtime());// md5 to generate the random string

$ResultStr = substr($RandomStr,0,5);//trim 5 digit 

$NewImage =imagecreatefrompng("img.png");//image create by existing image and as back ground 

      $noise_color = imagecolorallocate($NewImage, 235, 0, 40);
$TextColor = imagecolorallocate($NewImage, 255, 255, 255);//text color-white

      for( $i=0; $i<(75*25)/150; $i++ ) {
         imageline($NewImage, mt_rand(0,75), mt_rand(0,25), mt_rand(0,75), mt_rand(0,25), $noise_color);
      }

imagestring($NewImage, 5, 20, 10, $ResultStr, $TextColor);// Draw a random string horizontally 

$_SESSION['key'] = $ResultStr;// carry the data through session

header("Content-type: image/png");// out out the image 

imagepng($NewImage);//Output image to browser 

?>
