<?php

require 'bootstrap.php';

$board = new \Connect4\View\Board(6,7);
$playerOne = new \Connect4\Player\HumanPlayer();
$playerOne->setName("Human");
$playerTwo = new \Connect4\Player\DumbAiPlayer();
$playerTwo->setName("Robot");
$movesStore = new \Connect4\Store\MovesStore();


$game = new \Connect4\Game($board, $playerOne, $playerTwo, $movesStore);
$game->setup();
$game->start();

function dd($var)
{
    var_dump($var);
    die();
}