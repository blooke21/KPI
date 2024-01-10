<?php

function debug_to_console($data, $title) //console logs
{
    $output = $data;
    echo "<script>console.log('" . $title . ": " . $output . "' );</script>";
}

$url = 'https://kohlerpubliclibrary.events.mylibrary.digital/api/1.0/authorization';
$headers = [
    'Content-Type: application/json',
    'Cookie: PHPSESSID=67he5r63o0k1u6ctf9sc5mh13a',
];

$data = [
    'secretKey' => 'e51b3d6c007bde940fdd3d33b554b6ed',
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$positionStart = (strpos($response, 'token') + 8);


$token = substr($response, $positionStart, ((strpos($response, 'expires') - 3) - $positionStart));
debug_to_console($response, "Post Response");
debug_to_console($token, "Bearer Token");

curl_close($ch);

// Handle the response
if ($httpCode == 200) {
    // Successful request
    $eventUrl = 'https://kohlerpubliclibrary.events.mylibrary.digital/api/1.0/event/query?limit=4';
    $headers2 = [
        'Authorization: ' . $token,
        'Cookie: PHPSESSID=67he5r63o0k1u6ctf9sc5mh13a',
    ];


    $ch = curl_init($eventUrl);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);

    $response2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $trimmedResponse = substr($response2, ((strpos($response2, '"event_list":[') + 14)));
    $eventArray = explode('{"event_id"', $trimmedResponse);
    // print_r(array_values($eventArray));
    foreach ($eventArray as $event) {
        $event = explode(',', $event);
        echo "<br> Event: ";
        print_r(array_values($event));
    }

    curl_close($ch);

    // Handle the response
    if ($httpCode2 == 200) {
        // Successful request
        echo "<br>Response: <p>" . $trimmedResponse . "</p>";
    } else {
        // Handle the error
        echo "Error: HTTP Code - " . $httpCode . ", Response: " . $response;
    }

} else {
    // Handle the error
    echo "Error: HTTP Code - " . $httpCode . ", Response: " . $response;
}
?>