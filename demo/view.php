<?php
namespace Demo;

require __DIR__ . '/../vendor/autoload.php';

$DEMO_DOC_BASE_DIR  = realpath('../../web-content/').'/';

$file = isset($_GET['file']) ? $_GET['file'] : '';
$realFile = $DEMO_DOC_BASE_DIR . $file;

$book = 'doc-2.0';
$lang = 'cs';
$name = basename($realFile, '.texy');
$id = new \Wiki\PageId($book, $lang, $name);

$html = '';
$dir = $realFile;
if(is_file($realFile)) {
	$convertor = new \Wiki\Convertor;
	$convertor->paths['domain'] = 'nette.org';
	$convertor->paths['apiUrl'] = 'http://api.nette.org/2.3';
	$page = $convertor->parse($id, file_get_contents($realFile));
	$html = $page->html ?: '';
	$dir = dirname($realFile).'/';
}

echo '<!doctype html>
        <html>
        <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/combined.css" />
        <link rel="stylesheet" href="http://files.nette.org/css/combined.css" />
        </head>
        <body>
        <div class=page>
                <div class="main has-sidebar">
                        <div class=content>';
echo  $html;
echo "<hr />\n";
echo listDirectory($dir, $DEMO_DOC_BASE_DIR);
echo  '</div></div></div></body></html>';


function listDirectory($docDirectory, $DEMO_DOC_BASE_DIR)
{
	$s = '';
	$files = array();
	$d = opendir($docDirectory);
	while ($file = readdir($d)) {
		if ($file[0] === '.'  and $file !== '..') {
			continue;
		}
		$realFile = realpath($docDirectory.$file);
		if (is_dir($realFile)) {
			$file .= '/';
		}
		if(mb_strlen($DEMO_DOC_BASE_DIR) > mb_strlen($realFile.'/')) {
			continue;
		}
		$files[] = $file;
	}
	sort($files);
	$s = '<h3>'.basename(realpath($docDirectory)).'</h3>'."\n<ul>";
	$s = array_reduce(
		$files,
		function($s, $file)  use ($docDirectory, $DEMO_DOC_BASE_DIR) {
			$docDirectory = realpath($docDirectory);
			$fileDirectory = substr($docDirectory, strlen($DEMO_DOC_BASE_DIR)).'/';
			return $s.' <li><a href="?file='.urlencode($fileDirectory.$file).'">'.basename($file)."</a></li>\n";
		},
		$s
	);
	$s .= "</ul>";
	return $s;
}
