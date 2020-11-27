<?php
require 'vendor/simplehtmldom/simple_html_dom.php';

// Settings section
$origin = 'https://gitweb.dragonflybsd.org'; // Dragonflybsd repository
$url    = '/~corecode/nvidia.git/tree/fa1ab8b629a042d67d7a18cc163d2f0cf3fe8c12'; // Nvidia driver page
// Run parser
parsePage($origin, $url);

/**
* The recurcive function for grubing files
* from gitweb repository
* @param string $origin
* @param string $url
*/
function parsePage($origin, $url) {
    $strHtml = file_get_contents($origin . $url);
    $html    = str_get_html($strHtml);
    $links   = $html->find('a');
    $prefix  = getcwd();
    if (count($links)) {
        foreach ($links as $link) {
            // Page content
            $text = $link->innertext;
            // Go through directory tree
            if ($text === 'tree') {
                $innerUrl = $link->href;
                $folder = explode(':', $innerUrl)[1];
                createFolder($prefix . $folder);
                parsePage($origin, $innerUrl);
            }
            // Download single file
            if ($text === 'raw') {
                $innerUrl = $link->href;
                $path = explode(':', $innerUrl)[1];
                $url  = $origin . $innerUrl;
                $data = file_get_contents($url);
                writeFile($prefix . $path, $data);
            }
        }
    }
}

/**
* Write the data to a the file_exists
* die on error
* @param string $path
* @param string $data
*/
function writeFile($path, $data) {
    if (file_put_contents($path, $data)) {
        echo 'The file was saved successfully.' . $path . '</br>';
    } else {
        echo 'The file wasn\'t saved.' . $path . '</br>';
        die();
    }
}

/**
* Create a folder if not exist
* @param string $path
*/
function createFolder($path) {
    if (!file_exists($path)) {
        mkdir($path, 0750);
    }
}
