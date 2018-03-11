<?php

namespace Connect4;


use Connect4\Player\Player;
use Connect4\Store\MovesStore;
use Connect4\View\Board;

class Game
{
    const PLAYER_ONE_TOKEN = '[X]';
    const PLAYER_TWO_TOKEN = '[O]';

    /** @var Player $playerOne */
    private $playerOne;

    /** @var Player $playerTwo */
    private $playerTwo;

    /** @var Board board */
    private $board;

    /** @var Player $winner */
    private $winner;

    /** @var MovesStore $movesStore */
    private $movesStore;

    public function __construct(Board $board, Player $playerOne, Player $playerTwo, MovesStore $movesStore)
    {
        $this->board = $board;
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
        $this->movesStore = $movesStore;
    }

    public function setup()
    {
        $this->board->init()->draw();
        $this->movesStore->setCells($this->board->getCells());
        $this->playerOne->setToken(self::PLAYER_ONE_TOKEN);
        $this->playerTwo->setToken(self::PLAYER_TWO_TOKEN);
    }

    public function start()
    {
        $turn = 0;
        while (!$this->hasWinner())
        {
            $this->initiateMove($turn);

            $turn++;
        }

        echo "Congratulations! The winner is " . $this->getWinner();
    }

    private function initiateMove($turn)
    {
        // Player 1's turn
        $player = $this->playerOne;

        if ($turn % 2)
        {
            // If mod is 1 or true
            // Player 2's turn
            $player = $this->playerTwo;
        }

        $position = $player->enterPosition();

        $this->movesStore->dropToken($position, $player->getToken());
        $this->board->setCells($this->movesStore->getCells());
        $this->board->draw();
        die();
    }

    private function hasWinner()
    {
        return $this->getWinner();
    }

    private function getWinner()
    {
        if ($this->winner) return $this->winner->getName();
    }

    private function setWinner(Player $player)
    {
        $this->winner = $player;
    }

    private function checkWinner()
    {
        // loop to check the winner
//        $this->setWinner($this->playerOne);
    }


}