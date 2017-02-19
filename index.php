<?php
$colors = array('#F0F8FF', '#F7FFBF', '#E7FFBF', '#D7FFBF', '#C7FFBF');
$base_dir = joinPaths('.', 'reports');

function joinPaths() {
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }

    $paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
    $paths = array_filter($paths);
    return join(DIRECTORY_SEPARATOR, $paths);
}

function list_reports($directory) {
    global $base_dir;
    $dirFiles = array();
    if ($handle = opendir(joinPaths($base_dir, $directory))) {
        while (false !== ($entry = readdir($handle))) {
            if (!is_dir($entry)) {
                $dirFiles[] = $entry;
            }
        }
        sort($dirFiles);
        return $dirFiles;
    }
}

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

function render_reports($app, $reports) {
    global $colors;
    echo '<div class="reportslist">';
    // print_r($reports);
    foreach ($reports as $value) {
        echo '<div>';
        echo '<div class="cell report" style="background-color: ' . $colors[1] . ';"><a href="index.php?dir=' . $app . '&file=' . urlencode($value) . '&action=show">' . $value . '</a></div>';
        echo '<div class="cell delete" style="background-color: ' . $colors[1] . ';"><a href="index.php?dir=' . $app . '&file=' . urlencode($value) . '&action=delete">x</a></div>';
        echo '</div>';
    }
    echo '</div>';
}


echo '<!doctype html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Crash Reports</title>';

echo '
<style>
  div {
       padding: 5pt;
       margin-bottom: 1pt;
   }
   .main {
       margin-left: auto;
       margin-right: auto;
       width: auto;
   }
  .report {
       border-left: thick solid black;
       border-bottom: thin solid black;
       width: auto;
   }
  .key {
        position: relative;
        padding-left: 2em;
        border-left: thick solid black;
        border-bottom: thin solid black;
   }
   .key::before {
        content: \'\';
        position: absolute;
        border-color: gray;
        border-style: solid;
        border-width: 0 0.3em 0.3em 0;
        top: 0em;
        left: 0.4em;
        margin-top: 0em;
        transform: rotate(45deg);
        height: 1em;
        width: 1em;
   }
   .delete {
       border: thin solid black;
       text-align: center;
       width: 1em;
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
   div.reportslist {
       display:table;
       width: 100%
   }
   div.reportslist > div {
       display: table-row;
   }
   div.cell {
       display: table-cell;
   }
</style>';

echo '</head>';
echo '<body>';
echo '<div>';

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$file = isset($_GET['file']) ? $_GET['file'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($dir == "" && $file == "") {
    echo '<h1>Apps</h1>';
    if ($handle = opendir($base_dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            echo '<div>';
            if (is_dir(joinPaths($base_dir, $entry))) {
                echo '<div class="key" style="background-color: ' . $colors[0] . ';"><a href="index.php?dir=' . $entry . '">' . $entry . '</a></div>';
                echo '<div style="padding-left: 3em;">';
                render_reports($entry, list_reports($entry));
                echo '</div>';
            }
            echo '</div>';
        }
        closedir($handle);
    }
} elseif ($dir != "") {
    if ($file == "") {
        echo '<h1>Crash Reports for ' . $dir . '</h1>';
        render_reports($dir, list_reports($dir));
    } else {
        if ($action == 'show') {
            $json = json_decode(file_get_contents(joinPaths($base_dir, $dir, $file)), true);
            echo '<h1>' . $file . '</h1>';
            echo '<h2>' . $dir . '</h2>';
            echo '<div style="width: 100%;">';
            render_array($json, 0);
            echo '</div>';
        }
        if ($action == 'delete') {
            unlink(joinPaths($base_dir, $dir, $file));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}

echo '</div>';
echo '</body>';
echo '</html>';
?>
