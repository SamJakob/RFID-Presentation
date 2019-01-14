<?php

// kahoot-server v1

// INITIALIZE
$key = "JE043j15mD09uiwpwj45oi3jqoji";
if(!isset($_GET["key"]) || $_GET["key"] != $key) die("Invalid API key.");

if(!isset($_GET["action"])) die("Invalid action.");
$action = $_GET["action"];


// HELPER FUNCTIONS
function writeAndExit(array $data) : void {
    file_put_contents("kahoot.json", json_encode($data));
    exit;
}


$data = (array) json_decode(file_get_contents("kahoot.json"), true);
$questions = (array) json_decode(file_get_contents("questions.json"), true);

if($action == "begin"){
    $data = [
        "started" => false,
        "teams" => [

        ]
    ];
    writeAndExit($data);
}

if($action == "queryTeams"){
    header("Content-Type: text/json");
    echo json_encode($data["teams"], JSON_UNESCAPED_SLASHES);
    exit;
}

if($action == "commence"){
    $value = true;
    if(isset($_GET["value"])) $value = $_GET["value"] == true;
    
    $data["started"] = $value;
    writeAndExit($data);
}

if($action == "question"){
    
    header("Content-Type: text/json");
    if(!isset($_GET["questionNumber"]) || is_nan($_GET["questionNumber"]) || $_GET["questionNumber"] > count($questions) - 1 || $_GET["questionNumber"] < 0) die("{'q': 'No questions remaining.', 'a': {'red': '', 'yellow': '', 'blue': '', 'green': ''}}");
    
    $question = $_GET["questionNumber"];
    echo json_encode([
        'q' => $questions[$question]['question'],
        'a' => $questions[$question]['answers'],
        'correct' => $questions[$question]['correct']
    ]);
    
    exit;
}

if($action == "answers"){
    
    $results = [];
    
    header("Content-Type: text/json");
    foreach($data["teams"] as $team => $teamData){
        if($teamData["activeVote"]) $results[$team] = $teamData["activeVote"];
    }
    
    echo json_encode($results);
    exit;
}

if($action == "clearAnswers"){
    foreach($data["teams"] as $team => $teamData){
        unset($data["teams"][$team]["activeVote"]);
    }
    writeAndExit($data);
}