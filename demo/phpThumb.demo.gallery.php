<?php
//////////////////////////////////////////////////////////////
//   phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net      //
//         and/or https://github.com/JamesHeinrich/phpThumb //
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.gallery.php                                //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Demo showing basic usage of phpThumb in a photo gallery  //
//                                                          //
//////////////////////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>phpThumb :: sample photo gallery demo</title>
</head>
<body>
This is a demo of how you can use <a href="http://phpthumb.sourceforge.net">phpThumb()</a> in an image gallery.<br>
<hr>
<?php
die('For security reasons, this demo is disabled by default. Please comment out line '.__LINE__.' in '.basename(__FILE__));

$docroot = realpath((getenv('DOCUMENT_ROOT') && preg_match('#^'.preg_quote(realpath(getenv('DOCUMENT_ROOT'))).'#', realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace(DIRECTORY_SEPARATOR, '/',  __DIR__ )));
$imgdir = '/images/';                        // webroot-relative path to main images directory (only this and subdirectories of this will be displayed)
$thumb  = '/phpThumb.php';                   // webroot-relative path to "phpThumb.php"
$config = '/phpThumb.config.php';            // webroot-relative path to "phpThumb.php"
$popup  = '/demo/phpThumb.demo.showpic.php'; // webroot-relative path to "phpThumb.demo.showpic.php" (only used if $use_popup == true)
$thumbnailsize = 120;                        // size of thumbnails in pixels when browsing gallery
$displaysize   = 480;                        // size of large image display (popup or plain image) after clicking on thumbnail
$use_popup     = true;                       // if true, open large image in self-resizing popup window; if false, display larger image in main window

//////////////////////////////////////////////////////////////

require_once $docroot.$config;

$dirlimit = realpath($docroot.'/'.$imgdir);

$captionfile = $docroot.'/'.$imgdir.(@$_REQUEST['dir'] ? $_REQUEST['dir'].'/' : '').'captions.txt';
if (file_exists($captionfile)) {
	$filecontents = file($captionfile);
	foreach ($filecontents as $key => $value) {
		@list($photo, $caption) = explode("\t", $value);
		$CAPTIONS[$photo] = $caption;
	}
}

if (!empty($_REQUEST['pic'])) {

	$alt = @$CAPTIONS[$_REQUEST['pic']] ? $CAPTIONS[$_REQUEST['pic']] : $_REQUEST['pic'];
	echo '<img src="'.htmlentities(phpThumbURL('src='.urlencode($imgdir.@$_REQUEST['dir'].'/'.$_REQUEST['pic']).'&w='.$displaysize.'&h='.$displaysize, $thumb)).'" border="0" alt="'.htmlentities($alt, ENT_QUOTES).'"><br>';
	echo '<div align="center">'.htmlentities(@$CAPTIONS[$_REQUEST['pic']]).'</div>';

} else {

	$currentdir = realpath($docroot.'/'.$imgdir.@$_REQUEST['dir']);
	if (!preg_match('#^'.preg_quote($dirlimit).'#', $currentdir)) {
		echo 'Cannot browse to "'.htmlentities($currentdir).'"<br>';
	} elseif ($dh = @opendir($currentdir)) {
		$folders = array();
		$pictures = array();
		while ($file = readdir($dh)) {
			if (is_dir($currentdir.'/'.$file) && ($file{0} != '.')) {
				$folders[] = $file;
			} elseif (preg_match('#\\.(jpe?g|gif|png|bmp|tiff?)$#i', $file)) {
				$pictures[] = $file;
			}
		}
		closedir($dh);
		if (preg_match('#^'.preg_quote($dirlimit).'#', realpath($currentdir.'/..'))) {
			echo '<a href="'.htmlentities($_SERVER['PHP_SELF'].'?dir='.urlencode($_REQUEST['dir'].'/..'), ENT_QUOTES).'">Parent directory</a><br>';
		}
		if (!empty($folders)) {
			echo '<ul>';
			rsort($folders);
			foreach ($folders as $dummy => $folder) {
				echo '<li><a href="'.htmlentities($_SERVER['PHP_SELF'].'?dir='.urlencode(@$_REQUEST['dir'].'/'.$folder), ENT_QUOTES).'">'.htmlentities($folder).'</a></li>';
			}
			echo '</ul>';
		}
		if (!empty($pictures)) {
			foreach ($pictures as $file) {
				$alt = (!empty($CAPTIONS[$file]) ? $CAPTIONS[$file] : $file);
				echo '<table style="float: left;">'.(!empty($CAPTIONS[$file]) ? '<caption align="bottom">'.htmlentities($CAPTIONS[$file]).'</caption>' : '').'<tbody><tr><td>';
				if ($use_popup) {
					echo '<a title="'.htmlentities($alt, ENT_QUOTES).'" href="#" onClick="window.open(\''.$popup.'?src='.htmlentities($imgdir.@$_REQUEST['dir'].'/'.$file.'&w='.$displaysize.'&h='.$displaysize.'&title='.urlencode(@$CAPTIONS[$file] ? $CAPTIONS[$file] : $file)).'\', \'showpic\', \'width='.$displaysize.',height='.$displaysize.',resizable=no,status=no,menubar=no,toolbar=no,scrollbars=no\'); return false;">';
				} else {
					echo '<a title="'.htmlentities($alt, ENT_QUOTES).'" href="'.$_SERVER['PHP_SELF'].'?dir='.htmlentities(urlencode(@$_REQUEST['dir']).'&pic='.urlencode($file)).'">';
				}
				echo '<img src="'.htmlentities(phpThumbURL('src='.urlencode($imgdir.@$_REQUEST['dir'].'/'.$file).'&zc=1&w='.$thumbnailsize.'&h='.$thumbnailsize, $thumb)).'" border="1" width="'.$thumbnailsize.'" height="'.$thumbnailsize.'" alt="'.htmlentities($alt, ENT_QUOTES).'">';
				echo '</a></td></tr></tbody></table>';
			}
			echo '<br clear="all">';
		} else {
			echo '<i>No pictures in "'.htmlentities(str_replace(realpath($docroot), '', realpath($docroot.'/'.$imgdir.@$_REQUEST['dir']))).'"</i>';
		}
	} else {
		echo 'failed to open "'.htmlentities($currentdir).'"';
	}

}
?>
</body>
</html>