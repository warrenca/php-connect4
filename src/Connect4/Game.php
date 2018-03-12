<?php

namespace Connect4;


use Connect4\Player\Player;
use Connect4\Store\MovesStore;
use Connect4\View\Board;

class Game
{
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

    /** @var Player $currentPlayer */
    private $currentPlayer;

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
        $this->playerOne->setToken(Board::TOKEN_PLAYER_ONE);
        $this->playerTwo->setToken(Board::TOKEN_PLAYER_TWO);
    }

    public function start()
    {
        $turn = 0;
        while (!$this->hasWinner() && $turn < 42)
        {
            $this->initiateMove($turn);

            if ($this->checkWinner($this->getCurrentPlayer()))
            {
                break;
            }

            $turn++;
        }

        if ($this->getWinner())
        {
            echo "Congratulations! The winner is " . $this->getWinner()->getName();
        } else {
            echo "There is no winner.";
        }
    }

    private function initiateMove($turn)
    {
        // Player 1's turn
        $this->setCurrentPlayer($this->playerOne);

        if ($turn % 2)
        {
            // If mod is 1 or true
            // Player 2's turn
            $this->setCurrentPlayer($this->playerTwo);
        }

        $position = $this->getCurrentPlayer()->enterPosition();

        if (!$this->movesStore->dropToken($position, $this->getCurrentPlayer()->getToken()))
        {
            if ($this->getCurrentPlayer()->isHuman())
            {
                // Show only error to human and ignore error for robot
                $this->printError($this->movesStore->getError());
            }
            $this->initiateMove($turn);
        } else {
            $this->printInfo( sprintf('%s %s move is in the position %s', $this->getCurrentPlayer()->getName(), $this->getCurrentPlayer()->getToken(), $position) );
            $this->board->setCells($this->movesStore->getCells());
            $this->board->draw();
        }
    }

    private function printError($error)
    {
        echo "\033[31m $error \033[0m\n";
    }

    private function printInfo($info)
    {
        echo "\033[33m $info \033[0m\n";
    }

    private function hasWinner()
    {
        return $this->getWinner();
    }

    /**
     * @return Player
     */
    private function getWinner()
    {
        if ($this->winner) return $this->winner;
    }

    private function setWinner(Player $player)
    {
        $this->winner = $player;
    }

    private function checkWinner(Player $currentPlayer)
    {
        if ($this->movesStore->checkWinner($currentPlayer))
        {
            $this->setWinner($this->getCurrentPlayer());

            return true;
        }
    }

    /**
     * @return Player
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * @param Player $currentPlayer
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;
    }


}