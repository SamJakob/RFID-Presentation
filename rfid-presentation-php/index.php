<?php

if($_GET["action"] == "reset"){
    header("Content-Type: text/json");
    
    // Write a blank array into the JSON file
    file_put_contents("./data.json", json_encode(array("u" => date("U"))));
    
    // Stop execution.
    echo json_encode(array("success" => true));
    exit;
}

$data = (array) json_decode(file_get_contents("./data.json"));

function writeData($_data){
    file_put_contents("./data.json", json_encode($_data));
    
    if($_GET["then"] == "close"){
        // If it should close right away, print the close script
        header("Content-Type: text/html");
        echo "<script>window.close();</script>";
        exit;
    }
    
    // Stop execution.
    header("Content-Type: text/json");
    echo json_encode(array("success" => true));
    exit;
}

if($_GET["action"] == "get"){
    $key = $_GET["key"];
    
    header("Content-Type: text/plain");
    echo $data[$key];
}

if($_GET["action"] == "set"){
    $key = $_GET["key"];
    $value = $_GET["value"];
    
    $data[$key] = $value;
    writeData($data);
}