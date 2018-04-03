<?php
function friendly_url() {
    $request = $_SERVER["REQUEST_URI"];
    $request_split = mb_split("/", $request);
    $count_url = -1;
    
    foreach ($request_split as $value) {
        $count_url++;
    }
    
    $section = $request_split["$count_url"];
    return $section;
}
?>