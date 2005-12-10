<?php

require_once("./inc/config.inc.php");
require_once("./inc/database_handler.php");
require_once("./inc/nrp_api.php");

$account_id = $_GET['account_id'];

$space = $cfg['space'];

$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
$result = $bd->Query($query);
$role = $bd->FetchResult($result, 0, 'role');
$var_type = $role . '_type';
$var_color = $role . '_color';
$var_image = $role . '_icon';
$array_type = $cfg[$var_type];
$array_color = $cfg[$var_color];
$array_image = $cfg[$var_image];
if ($role == 'user')
	$person = List_People($account_id, '', '', '', '', $bd);

$num_blocks = count($cfg['time']);
$block_size = intval((600 - $num_blocks * $space) / $num_blocks);
$max_text_factor = (int) ($block_size / 7);

Get_Span_Limits(date('d'), date('m'), date('Y'), 'week', 0, $span_start_day, $span_start_month,
	$span_start_year, $span_end_day, $span_end_month, $span_end_year);

function locate($vector,$element, $n_elem)
{
	$found = 0;
        for ($i = 1; ($i <= $n_elem) and (!$found); $i++)
	{
                if ($vector[$i] == $element)
			$found = 1;
        }
	if (!$found)
		return -1;
	else return ($i-1);
}

function insert(&$vector, $element, &$n_elem)
// Inserts an element into a vector, if it does not exist
{
	if (locate($vector, $element,$n_elem) >=0) return 0;
	else {
		$n_elem++;
		$vector[$n_elem] = $element;
		return 1;
	}
}

$im = imagecreate(722, 435);

// Sets the image background to a light grey
imagecolorallocate($im,253,249,249);

$grey = imagecolorallocate($im,224,228,228);
$green = imagecolorallocate($im, 64, 131, 131);
$black = imagecolorallocate($im, 0, 0, 0);

ImageColorTransparent($im, $black);
ImageColorTransparent($im, $green);
ImageColorTransparent($im, $grey);

// Prints the squares of the header columns(times)
for ($i = 0; $i < count($cfg['time']); $i++)
{
	$grey = imagecolorallocate($im,224,228,228);
	imagefilledrectangle($im, $space + $i * ($block_size + $space), 0, ($block_size + $space) +
		$i * ($block_size + $space), 40, $grey);
}

// Prints the squares of the header lines (days)
for ($j = 1; $j < 8; $j++)
{
	$grey = imagecolorallocate($im,224,228,228);
	imagefilledrectangle($im, $space, 45 * $j, ($block_size + $space) , 85 + ($j - 1) * 45, $grey);
}

// Prints the titles of the header columns (times)
for ($i = 0; $i < $num_blocks - 1; $i++)
{
	ImageTTFText($im, 10, 90, 22 + ($block_size + $space) * ($i+1), ($block_size + $space + 2), $green,
		$cfg['bold_font'], $cfg['time'][$i]);
}
$im_norisk = imagecreatefrompng($cfg['directory'] . "images/nrp_logo_big.png");

// Shows the legend
for ($i = 0; $i < count($array_type); $i++)
{
	$color_hexa = $array_color[$i];
	$color_r = hexdec(substr($color_hexa, 0, 2));
	$color_g = hexdec(substr($color_hexa, 2, 2));
	$color_b = hexdec(substr($color_hexa, 4, 2));
	$color = imagecolorallocate($im, $color_r, $color_g, $color_b);
	imagefilledrectangle($im, 620, 55 + 30 * $i, 635, 70 + 30 * $i, $color);
	ImageTTFText($im, 10, 0, 640, 67 + 30 * $i, $green, $cfg['font'], $array_type[$i]);
}

// Prints the titles of the header lines (days)
ImageTTFText($im, 12, 90, 25, 80, $green, $cfg['font'], substr($cfg['days'][0], 0, 3));
ImageTTFText($im, 12, 90, 25, 125, $green, $cfg['font'], substr($cfg['days'][1], 0, 3));
ImageTTFText($im, 12, 90, 25, 172, $green, $cfg['font'], substr($cfg['days'][2], 0, 3));
ImageTTFText($im, 12, 90, 25, 215, $green, $cfg['font'], substr($cfg['days'][3], 0, 3));
ImageTTFText($im, 12, 90, 25, 256, $green, $cfg['font'], substr($cfg['days'][4], 0, 3));
ImageTTFText($im, 12, 90, 25, 304, $green, $cfg['font'], substr($cfg['days'][5], 0, 3));
ImageTTFText($im, 12, 90, 25, 348, $green, $cfg['font'], substr($cfg['days'][6], 0, 3));

// Start positions
$x_ret_ini = 40;
$y_ret_ini = 45;

$u_beg_date = mktime(1, 1, 1, $span_start_month, $span_start_day, $span_start_year);
$u_end_date = mktime(1, 1, 1, $span_end_month, $span_end_day, $span_end_year);

$legend = array();

for ($date = $u_beg_date; $date <= $u_end_date; $date += 24 * 60 * 60)
{
	$day = date('d', $date);
	$month = date('m', $date);
	$year = date('Y', $date);

	$cur_date = "$year-$month-$day";

	$apps = Retrieve_Appointments($account_id, $cur_date, $cur_date, '', '', '',
		$cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], 0, $bd);

	// If there are no appointments in the current day
	if (count($apps) == 0)
	{
		$white = imagecolorallocate($im, 255, 255, 255);
		$columns = $num_blocks - 1;
		imagefilledrectangle($im, $x_ret_ini, $y_ret_ini, $x_ret_ini + 30 + 35* ($columns - 1),
			$y_ret_ini + 40, $white);
	}
	else
	{
		for ($i = 0; $i < count($apps); $i++)
		{
			$before = $apps[$i][22];
			$after = $apps[$i][23];
			$length = $apps[$i][3];
			$description = $apps[$i][2];
			$color_hexa = $apps[$i][13];
			$color_r = hexdec(substr($color_hexa, 0, 2));
			$color_g = hexdec(substr($color_hexa, 2, 2));
			$color_b = hexdec(substr($color_hexa, 4, 2));
			if (($i == 0) && ($before > 0))
			{
				imagefilledrectangle($im, $x_ret_ini, $y_ret_ini, $x_ret_ini + 30 + 35 * ($before - 1),
					$y_ret_ini + 40, $white);
				$x_ret_ini = $x_ret_ini + ($space + $block_size) * $before;
			}

			$type_number = $apps[$i][11];

			if ($role == 'user')
			{
				$public_types = $person[0][7];
			}

			if (($public_types[$type_number]) || ($role != 'user'))
			{
				$back_color = imagecolorallocate($im, $color_r, $color_g, $color_b);
				imagefilledrectangle($im, $x_ret_ini, $y_ret_ini, $x_ret_ini + $block_size
					+ ($block_size + $space) * ($length - 1), $y_ret_ini + 40,
					$back_color);
			   	// If the text fits to the size
			   	if (strlen($description) <= ($max_text_factor + ($max_text_factor) *
					($length - 1)) )
			   	{
				 	ImageString($im, 2, $x_ret_ini + ($block_size + ($block_size + $space)
						*($length -1) - strlen($description) * 5) / 2,
						$y_ret_ini + ($block_size/2 - 2),$description,
					$black);
				}
				else
				{
				$ind_leg = locate($legend, $description, $n_elem);
					if ($ind_leg < 0){
						insert($legend, $description, $n_elem);
						$ind_leg = $n_elem;
					}
					ImageString($im, 2, $x_ret_ini + ($block_size + ($block_size + $space)
					 * ($length - 1) - strlen($ind_leg)*5)/2, $y_ret_ini + ($block_size/2 -
					2), $ind_leg, $black);
				}
			}
			else
			{
				imagefilledrectangle($im, $x_ret_ini, $y_ret_ini, $x_ret_ini + $block_size
					+ ($block_size + $space) * ($length - 1), $y_ret_ini + 40, $white);
			}
			$x_ret_ini = $x_ret_ini + ($space + $block_size) * $length;

			// If there are blank appointments after the current
			if ($after > 0)
			{
				$white = imagecolorallocate($im, 255, 255, 255);
				imagefilledrectangle($im, $x_ret_ini, $y_ret_ini, $x_ret_ini + $block_size
					+ ($space + $block_size) * ($after - 1), $y_ret_ini + 40, $white);
				$x_ret_ini = $x_ret_ini + ($space + $block_size) * $after;
			}
		}
	}

	// Updates the position to print the next day
	$x_ret_ini = $block_size + (2 * $space);
	$y_ret_ini += 45;
}

// Prints the legend
for ($i = 1; $i <= $n_elem; $i++)
{
	if ( ($i % 5 ) == 0) {
		$y = 365 + 4*13;
		$x = 5 + 190 * (((int) ($i/5)) -1);
	}
	else{
		 $y = 365 + (($i%5)-1 ) * 13;
		 $x = 5 + 190 * ((int) ($i/5));
	}
//	ImageTTFText($im, 10, 0, $x ,$y, $black, $cfg['font'], $i."-".$legend[$i]);
	ImageString($im, 2, $x, $y, $i."-".$legend[$i], $black);
}

imagerectangle($im,0 , 0, 721, 433, imagecolorallocate($im, 150, 150, 150));

imagecopyresized($im, $im_norisk, 600, 1, 0, 0, 118, 40, 270, 86);

header('Content-Type: image/png');
imagepng($im);

?>
