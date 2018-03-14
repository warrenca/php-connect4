<?php
// Let the game begin!

require 'bootstrap.php';

chooseGameMode:
printInfo("
Connect4 Game!
Please choose a game mode.
1) Human vs Human
2) Human vs AI
3) AI vs AI\n");

$gameMode = readline("Enter 1, 2 or 3: ");

if (!in_array($gameMode, [1,2,3]))
{
    printError("Invalid selection.");
    goto chooseGameMode;
}

/** @var \Connect4\Game $game */
switch ($gameMode)
{
    case 1:
        $game = $container->get('connect4.game.human.vs.human');
        break;
    case 2:
        $game = $container->get('connect4.game.human.vs.ai');
        break;
    case 3:
        $game = $container->get('connect4.game.ai.vs.ai');
        break;
    default:
        goto chooseGameMode;
}

$game->setup();
$game->start();

goto chooseGameMode;