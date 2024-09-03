<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Score</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body class="container mt-4">
<?php
session_start();
echo '<a href="?new_game=1" class="btn btn-danger mb-3">Нова гра</a>';
$players = getPlayers();
if (isset($_POST['done'])) {
    handleDoneForm($players);
    calculateScore($players);
}
getScoreTable($players);

if (isset($_POST['wanted'])) {
    handleWantedForm($players);
    doneForm($players);
} else {
    wantedForm($players);
}

function calculateScore(&$players)
{
    foreach ($players as &$player) {
        if ($player['wanted'] == $player['done']) {
            $score = $player['wanted'] == 0 ? 5 : $player['done'] * 10;
        } elseif ($player['wanted'] > $player['done']) {
            $score = ($player['wanted'] - $player['done']) * -10;
        } else {
            $score = $player['done'];
        }
        $player['score'] += $score;
        $player['wanted'] = 0;
        $player['done'] = 0;
    }
    $_SESSION['players'] = $players;
}

function handleDoneForm(&$players)
{
    foreach ($players as $key => &$player) {
        $player['done'] = $_POST['done'][$key];
    }
    $_SESSION['players'] = $players;
}

function doneForm(&$players)
{
    echo "<form method='post' class='mb-4'>";
    foreach ($players as $key => $player) {
        echo "<div class='form-group'>";
        echo "<label>" . $player['name'] . " (Wanted: " . $player['wanted'] . ")</label>";
        echo "<input type='number' class='form-control' name='done[$key]' placeholder='Enter done value'>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn btn-primary'>Submit</button>";
    echo "</form>";
}

function wantedForm(&$players)
{
    echo "<form method='post' class='mb-4'>";
    foreach ($players as $key => $player) {
        echo "<div class='form-group'>";
        echo "<label>" . $player['name'] . "</label>";
        echo "<input type='number' class='form-control' name='wanted[$key]' placeholder='Enter wanted value'>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn btn-primary'>Submit</button>";
    echo "</form>";
}

function handleWantedForm(&$players)
{
    foreach ($players as $key => &$player) {
        $player['wanted'] = $_POST['wanted'][$key];
    }
    $_SESSION['players'] = $players;
}

function getScoreTable($players)
{
    echo "<table class='table table-bordered table-striped text-center mb-4'>";
    echo "<thead class='thead-dark'><tr>";
    foreach ($players as $player) {
        echo "<th>" . $player['name'] . "</th>";
    }
    echo "</tr></thead>";
    echo "<tbody><tr>";
    foreach ($players as $player) {
        echo "<td>" . $player['score'] . "</td>";
    }
    echo "</tr></tbody>";
    echo "</table>";
}

function getPlayers()
{
    if (isset($_SESSION['players']) && !$_GET['new_game']){
        return $_SESSION['players'];
    }
    if (isset($_GET['players'])) {
        $players = [];
        $arr = $_GET['players'];
        foreach ($arr as $player) {
            $players[$player] = ['name' => $player, 'score' => 0, 'wanted' => 0, 'done' => 0];
        }
        $_SESSION['players'] = $players;
        return $players;
    }
    if (isset($_GET['players_count'])) {
        $playersCount = (int)$_GET['players_count'];
        echo "<form method='GET' class='mb-4'>";
        for ($i = 1; $i <= $playersCount; $i++) {
            echo "<div class='form-group'>";
            echo "<label>Гравець $i</label>";
            echo "<input type='text' class='form-control' name='players[]' placeholder='Enter player name'>";
            echo "</div>";
        }
        echo "<button type='submit' class='btn btn-primary'>Submit</button>";
        echo "</form>";
        die();
    }
    echo "<form method='GET' class='mb-4'>";
    echo "<div class='form-group'>";
    echo "<label>Введіть кількість гравців:</label>";
    echo "<input type='number' class='form-control' name='players_count' placeholder='Enter number of players'>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-primary'>Submit</button>";
    echo "</form>";
    die();
}
?>
