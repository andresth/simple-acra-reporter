<?php

// $reportid = isset($_PUT['reportid']) ? $_PUT['reportid'] : "";

$data = file_get_contents("php://input");

$json = json_decode($data);

if ($json == null) {
    http_response_code(406);
    exit();
}

/* Create directory if neccessary */
if (!is_dir("./reports/")) {
    mkdir("./reports/");
}
if (!is_dir("./reports/" . $json->{'BUILD_CONFIG'}->{'APPLICATION_ID'})) {
    mkdir("./reports/" . $json->{'BUILD_CONFIG'}->{'APPLICATION_ID'});
}

/* Open a file for writing */
$fp = fopen("./reports/" . $json->{'BUILD_CONFIG'}->{'APPLICATION_ID'} . "/" . $json->{'USER_CRASH_DATE'} . "_" . $json->{'REPORT_ID'} . ".json", "w");

fwrite($fp, json_encode($json, JSON_PRETTY_PRINT));

/* Close the streams */
fclose($fp);
// fclose($putdata);

http_response_code(204);

?>
