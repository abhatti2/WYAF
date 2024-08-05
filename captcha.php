<?php
session_start();

// Generate a random CAPTCHA code
$captcha_code = '';
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$characters_length = strlen($characters);
for ($i = 0; $i < 6; $i++) {
    $captcha_code .= $characters[rand(0, $characters_length - 1)];
}

// Store the CAPTCHA code in the session
$_SESSION['captcha'] = $captcha_code;

// Create the image
$width = 120;
$height = 40;
$image = imagecreate($width, $height);

// Set the colors
$background_color = imagecolorallocate($image, 255, 255, 255); // white background
$text_color = imagecolorallocate($image, 0, 0, 0); // black text

// Fill the background
imagefilledrectangle($image, 0, 0, $width, $height, $background_color);

// Add the text
$font_path = './Inter-VariableFont_slnt,wght.ttf';
$font_size = 20;
$angle = 0;
$x = 10;
$y = 30;
imagettftext($image, $font_size, $angle, $x, $y, $text_color, $font_path, $captcha_code);

// Set the content type
header('Content-Type: image/png');

// Output the image
imagepng($image);

// Clean up
imagedestroy($image);
?>
