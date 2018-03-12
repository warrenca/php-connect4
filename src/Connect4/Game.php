<?php

namespace Connect4;


use Connect4\Player\Player;
use Connect4\Store\MovesStore;
use Connect4\View\Board;

/**
 * Class Game
 * This class directs the game
 *
 * - It Sets the players turn,
 * - It Asks the MovesStore class to check if there a move is a winning turn or a valid one
 * - It Tells the Board class to print in the screen the valid moves
 * - Prints the move information and ends the game when there's a winner
 *
 * @package Connect4
 */
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

    /**
     * Game constructor.
     * @param Board $board
     * @param Player $playerOne
     * @param Player $playerTwo
     * @param MovesStore $movesStore
     */
    public function __construct(Board $board, Player $playerOne, Player $playerTwo, MovesStore $movesStore)
    {
        $this->board = $board;
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
        $this->movesStore = $movesStore;
    }

    /**
     * Game setup
     */
    public function setup()
    {
        // Set the players token
        $this->playerOne->setToken(Board::TOKEN_PLAYER_ONE);
        $this->playerTwo->setToken(Board::TOKEN_PLAYER_TWO);

        $this->printInfo(
            "Hey! Welcome to Connect4 game simulation.\n" .
                "It is a turn based game between two players.\n".
                "Each player simply needs to enter a column number and\n" .
                "try to Connect4 tokens of their own.\n" .
                "--------------------------------------\n" .
                "The players are...\n" .
                sprintf("Player One: Name %s, Token %s\n", $this->playerOne->getName(), $this->playerOne->getToken()) .
                sprintf("Player Two: Name %s, Token %s\n", $this->playerTwo->getName(), $this->playerTwo->getToken()) .
                Board::TOKEN_EMPTY_CELL . " indicates an empty cell and a valid drop point.\n" .
                "Have fun!\n\n"
        );

        // Initialise the board and print it on the screen
        $this->board->init();
        $this->board->draw();

        // Pass the cells to MovesStore
        $this->movesStore->setCells($this->board->getCells());
    }

    /**
     * Start the game!
     */
    public function start()
    {
        $turn = 0;
        $maximumTurns = Board::ROWS * Board::COLUMNS;

        // While there's no winner or the maximum turns hasn't been reached
        while (!$this->getWinner() && $turn < $maximumTurns)
        {
            $this->initiateMove($turn);

            if ($this->movesStore->checkWinningPatterns($this->getCurrentPlayer()))
            {
                $this->setWinner($this->getCurrentPlayer());
                // End the game since there's already a winner
                break;
            }

            $turn++;
        }

        if ($this->getWinner())
        {
            // There's a winner!
            $this->printSuccess("Congratulations! The winner is " . $this->getWinner()->getName());
        } else {
            $this->printError("There is no winner. :(");
        }
    }

    /**
     * Ask (human) or get (AI) the players desired move
     *
     * @param $turn
     */
    private function initiateMove($turn)
    {
        // It's Player 1's turn
        $this->setCurrentPlayer($this->playerOne);

        if ($turn % 2)
        {
            // If mod is 1 or true
            // It's Player 2's turn
            $this->setCurrentPlayer($this->playerTwo);
        }

        $columnIndex = $this->getCurrentPlayer()->enterColumn() - 1;

        // Drop the token to the designated column
        if (!$this->movesStore->dropToken($columnIndex, $this->getCurrentPlayer()->getToken()))
        {
            // Invalid dropping...
            if ($this->getCurrentPlayer()->isHuman())
            {
                // Show only the errors to human and ignore error for robot
                $this->printError($this->movesStore->getError());
            }

            // Ask to make another move since there's an error
            $this->initiateMove($turn);
        } else {
            // There's a valid move so we'll print that information
            $humanReadableColumn = "C" . ($columnIndex + 1);
            $this->printInfo( sprintf('%s %s move is in the position %s', $this->getCurrentPlayer()->getName(), $this->getCurrentPlayer()->getToken(), $humanReadableColumn) );

            // Set the cells to the board and draw it
            $this->board->setCells($this->movesStore->getCells());
            $this->board->draw();
        }
    }

    /**
     * @return Player
     */
    private function getWinner()
    {
        return $this->winner;
    }

    /**
     * @param Player $player
     */
    private function setWinner(Player $player)
    {
        $this->winner = $player;
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

    /**
     * Show success message in green!
     *
     * @param $message
     */
    private function printSuccess($message)
    {
        echo "\033[42m$message \033[0m\n";
    }

    /**
     * Show error message in red!
     *
     * @param $error
     */
    private function printError($error)
    {
        echo "\033[31m$error \033[0m\n";
    }

    /**
     * Show info message in yellow!
     *
     * @param $info
     */
    private function printInfo($info)
    {
        echo "\033[33m$info \033[0m\n";
    }
}