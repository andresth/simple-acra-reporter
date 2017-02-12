<?php

$colors = array('#F0F8FF', '#F7FFBF', '#E7FFBF', '#D7FFBF', '#C7FFBF');

function render_array($array, $level) {
    global $colors;
    foreach ($array as $key => $value) {
        echo '<div style="margin-left: ' . $level * 3 . 'em; background-color: ' . $colors[$level] . ';">';
        echo '<b>' . $key . '</b>';
        echo '</div>';
        if (!is_array($value)) {
            echo '<div style="margin-left: ' . ($level + 1) * 3 . 'em;"><pre>' . $value . '</pre></div>';
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
  table, td, th { border: 1px solid black; }
  div { padding: 0.2em; border: 1px solid grey; }
  div > div { margin-left: 3em; }
</style>';

echo '  </head>';

echo '  <body>';

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$file = isset($_GET['file']) ? $_GET['file'] : '';

if ($dir == "" && $file == "") {
    echo '    <h1>Apps</h1>';
    if ($handle = opendir('./reports/')) {
        echo '    <ul>';
        /* Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
        while (false !== ($entry = readdir($handle))) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            if (is_dir($entry)) {
                echo '      <li>' . '<a href="index.php?dir=' . $entry . '">' . $entry . '</a>' . '</li>';
            }
        }
        closedir($handle);
        echo '    </ul>';
    }
} elseif ($dir != "") {
    if ($file == "") {
        $dirFiles = array();
        echo '    <h1>Crash Reports</h1>';
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
            echo '    <ul>';
            foreach ($dirFiles as $value) {
                echo '      <li>' . '<a href="index.php?dir=' . $dir . '&file=' . urlencode($value) . '">' . $value . '</a>' . '</li>';
            }
            echo '    </ul>';
        }
    } else {
        $json = json_decode(file_get_contents('./reports/' . $dir . '/' . $file), true);
        // $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($json), RecursiveIteratorIterator::SELF_FIRST);
        echo '<h1>' . $file . '</h1>';
        render_array($json, 0);
        // echo '<table>';
        // foreach ($json as $key => $value) { // This will search in the 2 jsons
        //     echo '<tr>';
        //     echo '<td>' . $key . '</td>';
        //     if (!is_array($value)) {
        //         echo '<td colspan=2><pre><code>' . $value . '</code></pre></td>';
        //     } else {
        //         echo '</tr>';
        //         foreach ($value as $key2 => $value2) {
        //             echo '<tr>';
        //             echo '<td></td>';
        //             echo '<td>' . $key2 . '</td>';
        //             echo '<td>' .$value2 . '</td>';
        //             echo '</tr>';
        //         }
        //     }
        // }
        // echo '</table>';
    }
}

echo '  </body>';

echo '</html>';
?>
