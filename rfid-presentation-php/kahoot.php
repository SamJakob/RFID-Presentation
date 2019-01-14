<?php

session_start();

if(!isset($_GET["team"]) && !isset($_POST["team"]) && !isset($_SESSION["team"])) die("Invalid team ID.");

$data = (array) json_decode(file_get_contents("kahoot.json"), true);

$team = -1;
if (isset($_GET["team"])) $team = intval($_GET["team"]);
if (isset($_POST["team"])) $team = intval($_POST["team"]);
if (isset($_SESSION["team"])) $team = intval($_SESSION["team"]);
if($team != 1 && $team != 2) die("Invalid team.");

$currentTeam = $data["teams"][strval($team)];

function writeData($data){
    file_put_contents("kahoot.json", json_encode($data));
}

$action = isset($_GET["action"]) ? $_GET["action"] : "";
if($action == "vote"){
    // Obviously, we only want to handle voting if the game has started
    if($data["started"]){
        // Get the color
        if(!isset($_GET["color"])) die("Invalid color.");
        
        // Check the color
        $color = $_GET["color"];
        if($color != "red" && $color != "yellow" && $color != "blue" && $color != "green") die("Invalid color.");
        
        // Write the color and exit.
        $data["teams"][strval($team)]["activeVote"] = $color;
        writeData($data);
        
        header("Content-Type: text/html");
        echo "<script>window.close();</script>";
        exit;
    }
}

if($action == "join"){
    if($data["teams"][strval($team)]["ready"]){
        header("Location: kahoot.php");
        exit;
    }
    
    $data["teams"][strval($team)]["name"] = htmlspecialchars($_POST["name"]);
    $data["teams"][strval($team)]["ready"] = true;
    $_SESSION["team"] = $team;
    writeData($data);
    header("Location: kahoot.php");
    exit;
}

if($action == "quit"){
    unset($data["teams"][strval($team)]);
    writeData($data);
    session_destroy();
    header("Location: kahoot.php?team=$team");
    exit;
}

if($action == "end"){
    session_destroy();
    echo "<h1>Thanks for playing!</h1>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sam and Dwend's Kahoot</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    
    <?php if(!$currentTeam["ready"]){ ?>
    
    <center>
        
        <h1>Welcome!</h1>
        <p>You are team <?php echo $team; ?></p>
        <br>
        <form method="POST" action="kahoot.php?action=join">
            <input type="hidden" name="team" value="<?php echo $team; ?>">
            <input type="text" placeholder="Team Name" name="name" required>
            <button>Join Game</button>
        </form>
        
    </center>
    
    <?php }else{ ?>
    
    <center>
        <h1>Let's go, <?php echo $currentTeam["name"]; ?>!</h1>
        <br>
        <p><a href="?action=quit">Quit</a></p>
    </center>
    
    <?php } ?>
    
</body>
</html>