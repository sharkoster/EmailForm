<?php

// set the name of the cookie to save the captcha code
define('CAPTCHA_COOKIE', 'imgcaptcha_');

/*
	Initialize the random number generator.
*/

mt_srand(time());

/*

Define the path to the folder with fonts.
and a list of file names with fonts in it -
From this list we will choose a random font each time.

*/

define('PATH_TTF', 'fonts/');
$fonts = array('liber-mono.ttf', 'liber-sans.ttf');

/*

The main parameters of the captcha.

To support different parameters, captcha can be created here.
multidimensional array and refer to it by index.

*/

$par = array(
	// captcha width
	'WIDTH' => 120,
	// captcha height
	'HEIGHT' => 32,
	// captcha font size
	'FONT_SIZE' => 14,

	// number of characters on captcha
	'CHARS_COUNT' => 5,
	// allowed captcha characters
	'ALLOWED_CHARS' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23458',

	// captcha background color - white in our case
	'BG_COLOR' => '#FFFFFF',
	// number of lines on captcha
	'LINES_COUNT' => 3,
	// line thickness
	'LINES_THICKNESS' => 2
);

/*
	Common Captcha Parameters
*/

// character colors
define('CODE_CHAR_COLORS', '#880000,#008800,#000088,#888800,#880088,#008888,#000000');
// line colors
define('CODE_LINE_COLORS', '#880000,#008800,#000088,#888800,#880088,#008888,#000000');

// we get the colors of lines and symbols into arrays for random sampling later
$line_colors = preg_split('/,\s*?/', CODE_LINE_COLORS);
$char_colors = preg_split('/,\s*?/', CODE_CHAR_COLORS);

// create an empty picture and fill it with a white background
$img = imagecreatetruecolor($par['WIDTH'], $par['HEIGHT']);
imagefilledrectangle($img, 0, 0, $par['WIDTH'] - 1, $par['HEIGHT'] - 1, gd_color($par['BG_COLOR']));

// set the thickness of the lines and display them on the captcha
imagesetthickness($img, $par['LINES_THICKNESS']);

for ($i = 0; $i < $par['LINES_COUNT']; $i++)
    imageline($img,
        mt_rand(0, $par['WIDTH'] - 1),
        mt_rand(0, $par['HEIGHT'] - 1),
        mt_rand(0, $par['WIDTH'] - 1),
        mt_rand(0, $par['HEIGHT'] - 1),
        gd_color($line_colors[mt_rand(0, count($line_colors) - 1)])
    );

// Variable to store captcha code
$code = '';

// Set the coordinate in the center of the Y axis
$y = ($par['HEIGHT'] / 2) + ($par['FONT_SIZE'] / 2);

// Display characters on captcha
for ($i = 0; $i < $par['CHARS_COUNT']; $i++) {
		// choose a random color from the available set
    $color = gd_color($char_colors[mt_rand(0, count($char_colors) - 1)]);
    // determine the random angle of the symbol's heel from -45 to 45 degrees
    $angle = mt_rand(-45, 45);
    // choose a random character from the available set
    $char = substr($par['ALLOWED_CHARS'], mt_rand(0, strlen($par['ALLOWED_CHARS']) - 1), 1);
    // choose a random font from the available set
    $font = PATH_TTF . $fonts[mt_rand(0, count($fonts) - 1)];
    // calculate the current character coordinate along the X axis
    $x = (intval(($par['WIDTH'] / $par['CHARS_COUNT']) * $i) + ($par['FONT_SIZE'] / 2));
    
    // display the character on the captcha
    imagettftext($img, $par['FONT_SIZE'], $angle, $x, $y, $color, $font, $char);
    
    // save captcha code
    $code .= $char;
}

// keep the captcha in the cookie for further verification
setcookie(CAPTCHA_COOKIE, md5($code));

/*
	We send the generated image to the browser and get rid of it,
although the garbage collector usually does it for us
	
*/

header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);

// Convert HTML 6-character color to GD color
function gd_color($html_color)
{
  return preg_match('/^#?([\dA-F]{6})$/i', $html_color, $rgb)
    ? hexdec($rgb[1]) : false;
}
