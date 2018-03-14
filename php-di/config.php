<?php

/**
 * PHP-DI package configuration
 * Contains all of the definitions of classes used in the application
 */

use Connect4\Store\MovesStore;
use Connect4\View\Board;

return [
    // Board Instantiation
    'connect4.view.board'       => \DI\create(Board::class),
    // MoveStore Instantiation
    'connect4.store.movesStore' => \DI\create(MovesStore::class),
    // Human 1 Player Instantiation
    'connect4.player.human'     => function(\Psr\Container\ContainerInterface $c) {
            $player = new \Connect4\Player\HumanPlayer();
            $player->setName("Human 👤");
            $player->setMovesStore($c->get('connect4.store.movesStore'));
            return $player;
    },
    // Human 2 Player Instantiation
    'connect4.player.human2'    => function(\Psr\Container\ContainerInterface $c) {
        $player = new \Connect4\Player\HumanPlayer();
        $player->setName("Human 2 👤");
        $player->setMovesStore($c->get('connect4.store.movesStore'));
        return $player;
    },
    // AI 1 Player Instantiation
    'connect4.player.ai'        => function(\Psr\Container\ContainerInterface $c)
    {
        $player = new \Connect4\Player\AiPlayer\DumbAiPlayer();
        $player->setName("Robot 🤖");
        $player->setMovesStore($c->get('connect4.store.movesStore'));
        return $player;
    },
    // AI 2 Player Instantiation, used in the test
    'connect4.player.smarterAi' => function(\Psr\Container\ContainerInterface $c)
    {
        $player = new \Connect4\Player\AiPlayer\SmarterAiPlayer();
        $player->setName("Smarter Robot 🤖");
        $player->setMovesStore($c->get('connect4.store.movesStore'));
        return $player;
    },
    // Game Instantiation for Human v AI
    'connect4.game.human.vs.ai'     => function(\Psr\Container\ContainerInterface $c)
    {
        return new \Connect4\Game(  $c->get('connect4.view.board'),
                                    $c->get('connect4.player.human'),
                                    $c->get('connect4.player.smarterAi'),
                                    $c->get('connect4.store.movesStore')
                                  );
    },
    // Game Instantiation for Human v Human
    'connect4.game.human.vs.human'  => function(\Psr\Container\ContainerInterface $c)
    {
        return new \Connect4\Game(  $c->get('connect4.view.board'),
            $c->get('connect4.player.human'),
            $c->get('connect4.player.human2'),
            $c->get('connect4.store.movesStore')
        );
    },
    // Game for testing Instantiation
    'connect4.game.ai.vs.ai'        => function(\Psr\Container\ContainerInterface $c)
    {
        return new \Connect4\Game(  $c->get('connect4.view.board'),
            $c->get('connect4.player.ai'),
            $c->get('connect4.player.smarterAi'),
            $c->get('connect4.store.movesStore')
        );
    }
];