<?php
// Let the game begin!

require 'bootstrap.php';

$game = $container->get('connect4.game');
$game->setup();
$game->start();