<?php

require 'bootstrap.php';

$board = new \Connect4\View\Board();
$movesStore = new \Connect4\Store\MovesStore();

$playerOne = new \Connect4\Player\HumanPlayer();
$playerOne->setName("Human 👤");
$playerOne->setMovesStore($movesStore);

$playerTwo = new \Connect4\Player\DumbAiPlayer();
$playerTwo->setName("Robot 🤖");
$playerTwo->setMovesStore($movesStore);


$game = new \Connect4\Game($board, $playerOne, $playerTwo, $movesStore);
$game->setup();
$game->start();