<?
/*
No Risk Planning 2.0 
GNU/GPL License
http://incubadora.fapesp.br/projects/nrp

Universidade de São Paulo
Instituto de Ciências Matemáticas e de Computação
Departamento de Ciências de Computação e Estatística
Intermidia Laboratory
http://www.intermidia.icmc.usp.br

This project is funded by FAPESP

São Carlos - SP - Brazil

Developers Team:
	André Pimenta Freire
	Renata Pontin de Mattos Fortes

Former Members:
	Marcos Vinícius Moura
	Tyciano Maia Ribeiro

This is the configuration file.
Set it properly according to your needs.
The main features of the system will be defined by these parameters.
*/

$cfg['db_type']               = 'mysql';        // Database type
$cfg['server_db']             = 'localhost';    // Database server address
$cfg['user_db']               = 'user';     // Database username
$cfg['password_db']           = 'senha';      // Database password
$cfg['bdname']                = 'nrp4';         // Database name

$cfg['institution_name']     = 'Universidade de São Paulo'; // Name of the Institution
$cfg['institution_acronym']   = 'USP';          // Institution's acronym

$cfg['url']                   = 'http://vinho.intermidia.icmc.usp.br/norisk/';  // URL of the directory where the system runs
$cfg['directory']             = '/srv/www/default/html/norisk/';  // Path of the server directory where the system runs
$cfg['docs_directory']        = 'docs/'; // Path of the directory to upload group documents
$cfg['admin_email']           = 'apfreire@grad.icmc.usp.br';
// E-mail of the admin (will be used to mails sent by the system)

$cfg['max_doc_size']          = 50000;          // Maximum size to upload group documents

$cfg['default_schedule']      = 'week';         // Choose between 'day', 'week', 'month' or 'semester'

$cfg['professor_category']    = 'professor';    // Name of the category of professor users (owner of courses)

$cfg['default_xsl']           = 'default';      // Name of the default set of XSL to be used
$cfg['enable_server_transf']  = 1;              // Enables the transformation of XML+XSLT server side

$cfg['default_room_type'] = 0;

$cfg['group_notices_number'] = 5;               // Default Number of groups notices to be shown

// Sets the types of appointments which will be available for common users
$cfg['user_type'][0]          = 'Common';       // Name of the type
$cfg['user_color'][0]         = 'FFCC66';       // Color of the appointments of this type
$cfg['user_icon'][0]          = '';             // Image to be shown in appointments of this type

$cfg['user_type'][1]          = 'Academic';
$cfg['user_color'][1]         = 'E8F3FF';
$cfg['user_icon'][1]          = 'images/academic.png';

$cfg['user_type'][2]          = 'Leisure';
$cfg['user_color'][2]         = '99CC00';
$cfg['user_icon'][2]          = 'images/leisure.png';

$cfg['user_type'][3]          = 'Important';
$cfg['user_color'][3]         = 'FF633E';
$cfg['user_icon'][3]          = 'images/important.png';

// Sets the types of appointments which will be available for courses
$cfg['course_type'][0]        = 'Class';
$cfg['course_color'][0]       = '80D0D0';
$cfg['course_icon'][0]        = './images/class_icon.gif';

$cfg['course_type'][1]        = 'Student Assistance';
$cfg['course_color'][1]       = 'FFCC66';
$cfg['course_icon'][1]        = '';

$cfg['course_type'][2]        = 'Laboratory';
$cfg['course_color'][2]       = '99CC00';
$cfg['course_icon'][2]        = '';

// Sets the types of appointments which will be available for rooms
$cfg['room_type'][0]        = 'Common';
$cfg['room_color'][0]       = '80D0D0';
$cfg['room_icon'][0]        = '';

$cfg['room_type'][1]        = 'Meeting';
$cfg['room_color'][1]       = 'FFCC66';
$cfg['room_icon'][1]        = '';


// Sets the days order and the days of the week
$cfg['days'][0]               = 'Sunday';
$cfg['days'][1]               = 'Monday';
$cfg['days'][2]               = 'Tuesday';
$cfg['days'][3]               = 'Wednesday';
$cfg['days'][4]               = 'Thursday';
$cfg['days'][5]               = 'Friday';
$cfg['days'][6]               = 'Saturday';

// Sets the months order
$cfg['months'][0]             = 'January';
$cfg['months'][1]             = 'February';
$cfg['months'][2]             = 'March';
$cfg['months'][3]             = 'April';
$cfg['months'][4]             = 'May';
$cfg['months'][5]             = 'June';
$cfg['months'][6]             = 'July';
$cfg['months'][7]             = 'August';
$cfg['months'][8]             = 'September';
$cfg['months'][9]             = 'October';
$cfg['months'][10]            = 'November';
$cfg['months'][11]            = 'December';

$cfg['date_format']           = 'm/d/Y';

// Sets the periodicity of the appointments
// There is one pre-configured set of options.
// To chose it, comment the other sets of options,
// and set yours.

// These commands set an one hour intervals from 7 to 23h
$cfg['first_time']         = 7;
$cfg['last_time']          = 23;
$time_counter = 0;
for ($i = $cfg['first_time']; $i <= $cfg['last_time']; $i++)
{
	if ($i < 7)
		$hour = '0' . $i;
	else
		$hour = $i;
	$cfg['time'][$time_counter++] = $hour . ":00";
}


// These commands set half an hour intervals from 7 to 23h

/*$time_counter = 0;
for ($i = $cfg['first_time']; $i <= $cfg['last_time']; $i++)
{
        if ($i < 7)
                $hour = '0' . $i;
        else
                $hour = $i;
        $cfg['time'][$time_counter++] = $hour . ":00";
	$cfg['time'][$time_counter++] = $hour . ":30";
}*/

// These commands set a customized set of intervals
/*
$cfg['time'][0] = '07:20';
$cfg['time'][1] = '08:10';
$cfg['time'][2] = '09:20';
$cfg['time'][3] = '10:10';
$cfg['time'][4] = '11:10';
$cfg['time'][5] = '12:00';
$cfg['time'][6] = '13:20';
*/

// The configurations below will be used to generate the schedule .png image

$cfg['space'] = 5;                 // Space between squares (in pixels)
$cfg['bold_font'] = '/home/andre/arialbd.ttf'; // Path to the TTF file for Bold arial font
$cfg['font'] = '/home/andre/arial.ttf'; // Path to the TTF file for arial

?>
