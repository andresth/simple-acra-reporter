<?php
$colors = array('#F0F8FF', '#F7FFBF', '#E7FFBF', '#D7FFBF', '#C7FFBF');

function render_array($array, $level) {
    global $colors;
    foreach ($array as $key => $value) {
        echo '<div class="key" style="margin-left: ' . $level * 3 . 'em; background-color: ' . $colors[$level] . ';">';
        echo '<b>' . $key . '</b>';
        echo '</div>';
        if (!is_array($value)) {
            echo '<div class="content" style="margin-left: ' . ($level + 1) * 3 . 'em;"><pre>' . $value . '</pre></div>';
        } else {
            render_array($value, $level + 1);
        }
    }
}


echo '<!doctype html>';
echo '<html lang="en">';
echo '  <head>';
echo '    <meta charset="utf-8">';
echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '    <title>Crash Reports</title>';

echo '
<style>
  div {
       padding: 5pt;
       margin-bottom: 1pt;
   }
  .key {
       border-left: thick solid black;
       border-bottom: thin solid black;
   }
   .content {
       background-color: #fdfdfd;
       border-bottom: thin dashed black;
       border-left: thin dashed black;
       margin-bottom: 6pt;
   }
   h1 {
       border-left: thick solid black;
       border-bottom: thin solid black;
       padding: 5pt;
   }
   h2 {
       border-left: thick solid black;
       border-bottom: thin solid black;
       padding: 5pt;
       margin-left: 1em;
   }
</style>';

echo '  </head>';
echo '  <body>';

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$file = isset($_GET['file']) ? $_GET['file'] : '';

if ($dir == "" && $file == "") {
    echo '    <h1>Apps</h1>';
    if ($handle = opendir('./reports')) {
        // echo '    <ul>';
        while (false !== ($entry = readdir($handle))) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            if (is_dir('./reports/' . $entry)) {
                echo '<div class="key" style="background-color: ' . $colors[0] . ';">' . '<a href="index.php?dir=' . $entry . '">' . $entry . '</a>' . '</div>';
            }
        }
        closedir($handle);
        // echo '    </ul>';
    }
} elseif ($dir != "") {
    if ($file == "") {
        $dirFiles = array();
        echo '<h1>Crash Reports for ' . $dir . '</h1>';
        if ($handle = opendir('./reports/' . $dir)) {
            while (false !== ($entry = readdir($handle))) {
                // if ($entry == '.' || $entry == '..') {
                //     continue;
                // }
                if (!is_dir($entry)) {
                    $dirFiles[] = $entry;
                }
            }
            closedir($handle);
            sort($dirFiles);
            // echo '    <ul>';
            foreach ($dirFiles as $value) {
                echo '<div class="key" style="background-color: ' . $colors[1] . ';">' . '<a href="index.php?dir=' . $dir . '&file=' . urlencode($value) . '">' . $value . '</a>' . '</div>';
            }
            // echo '    </ul>';
        }
    } else {
        $json = json_decode(file_get_contents('./reports/' . $dir . '/' . $file), true);
        // $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($json), RecursiveIteratorIterator::SELF_FIRST);
        echo '<h1>' . $file . '</h1>';
        echo '<h2>' . $dir . '</h2>';
        render_array($json, 0);
    }
}

echo '  </body>';
echo '</html>';
?>
