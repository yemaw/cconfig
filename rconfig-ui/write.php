<?php

function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

if($_POST['file'] && $_POST['content']){
    if(isJson($_POST['content'])){
        file_put_contents($_POST['file'], $_POST['content']);
        echo 'Success';
    } else {
        echo 'Error: Invalid JSON format';
    }

}