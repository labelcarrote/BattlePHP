<?php
/**
 * A very simple stats counter for all kind of stats about a development folder
 * 
 * @author Joel Lord
 * @copyright Engrenage (www.engrenage.biz)
 * 
 * For more information: joel@engrenage.biz

 */


$fileCounter = array();
$fileCounter['gen']['commentedLines'] = 0;
$fileCounter['gen']['functions'] = 0;
$fileCounter['gen']['commentedLines'] = 0;
$fileCounter['gen']['classes'] = 0;
$fileCounter['gen']['commentBlocks'] = 0;
$fileCounter['gen']['blankLines'] = 0;
$fileCounter['gen']['totalFiles'] = 0;

$totalLines = countLines('./battle/app', $fileCounter); 
echo $totalLines." lines in the current folder<br>";
echo $totalLines - $fileCounter['gen']['commentedLines'] - $fileCounter['gen']['blankLines'] ." actual lines of code (not a comment or blank line)<br><br>";

foreach($fileCounter['gen'] as $key=>$val) {
    echo ucfirst($key).":".$val."<br>";
}

echo "<br>";

foreach($fileCounter as $key=>$val) {
    if(!is_array($val)) echo strtoupper($key).":".$val." file(s)<br>";
}


function countLines($dir, &$fileCounter) {
    $_allowedFileTypes = "(html|htm|phtml|tpl|php|js|css|ini|md|htaccess)";
    $lineCounter = 0;
    $dirHandle = opendir($dir);
    $path = realpath($dir);
    $nextLineIsComment = false;

    if($dirHandle) {
        while(false !== ($file = readdir($dirHandle))) {
            if(is_dir($path."/".$file) && ($file !== '.' && $file !== '..')) {
                $lineCounter += countLines($path."/".$file, $fileCounter);
            } elseif($file !== '.' && $file !== '..') {
                //Check if we have a valid file 
                $ext = _findExtension($file);
                if(preg_match("/".$_allowedFileTypes."$/i", $ext)) {
                    $realFile = realpath($path)."/".$file;
                    $fileHandle = fopen($realFile, 'r');
                    $fileArray = file($realFile);
                    //Check content of file:
                    for($i=0; $i<count($fileArray); $i++) {
                        if($nextLineIsComment) {
                            $fileCounter['gen']['commentedLines']++;
                            //Look for the end of the comment block
                            if(strpos($fileArray[$i], '*/')) {
                                $nextLineIsComment = false;
                            }
                        } else {
                            //Look for a function
                            if(strpos($fileArray[$i], 'function')) {
                                $fileCounter['gen']['functions']++;
                            }
                            //Look for a commented line
                            if(strpos($fileArray[$i], '//')) {
                                $fileCounter['gen']['commentedLines']++;
                            }
                            //Look for a class
                            if(substr(trim($fileArray[$i]), 0, 5) == 'class') {
                                $fileCounter['gen']['classes']++;
                            }
                            //Look for a comment block
                            if(strpos($fileArray[$i], '/*')) {
                                $nextLineIsComment = true;
                                $fileCounter['gen']['commentedLines']++;
                                $fileCounter['gen']['commentBlocks']++;
                            }
                            //Look for a blank line
                            if(trim($fileArray[$i]) == '') {
                                $fileCounter['gen']['blankLines']++;
                            }
                        }

                    }
                    $lineCounter += count($fileArray);
                }
                //Add to the files counter
                $fileCounter['gen']['totalFiles']++;
                if(!isset($fileCounter[strtolower($ext)]))
                	$fileCounter[strtolower($ext)] = 0;
                $fileCounter[strtolower($ext)]++;
            }
        }
    } else echo 'Could not enter folder';

    return $lineCounter;
}

function _findExtension($filename) {
	return pathinfo($filename, PATHINFO_EXTENSION);
}