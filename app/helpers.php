<?php

function apiResponse($data,$message = '',$statusCode = 200){
    $message = (is_array($message)) ? reset($message) : $message;
    $response['data'] = ($data) ?? [];
    $response['message'] = (is_array($message)) ? $message[0] : $message;
    return response()->json($response,$statusCode);
}

?>