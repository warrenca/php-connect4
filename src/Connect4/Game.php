<?php

namespace Connect4;


use Connect4\Player\PlayerInterface;
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
    /** @var PlayerInterface $playerOne */
    private $playerOne;

    /** @var PlayerInterface $playerTwo */
    private $playerTwo;

    /** @var Board board */
    private $board;

    /** @var PlayerInterface $winner */
    private $winner;

    /** @var MovesStore $movesStore */
    private $movesStore;

    /** @var PlayerInterface $currentPlayer */
    private $currentPlayer;

    /** @var int $maximumTurns */
    private $maximumTurns;

    /**
     * Game constructor.
     * @param Board $board
     * @param MovesStore $movesStore
     */
    public function __construct(Board $board, MovesStore $movesStore)
    {
        $this->setBoard($board);
        $this->setMovesStore($movesStore);
        $this->setMaximumTurns(Board::COLUMNS * Board::ROWS);
    }

    /**
     * Game setup
     */
    public function setup()
    {
        // Set the players token
        $this->getPlayerOne()->setToken(Board::TOKEN_PLAYER_ONE);
        $this->getPlayerTwo()->setToken(Board::TOKEN_PLAYER_TWO);

        printInfo(
            "Hey! Welcome to Connect4 game.\n" .
            "It is a turn-based game between two players.\n" .
            "Each player simply needs to enter a column number \n" .
            "where they want to drop their token.\n" .
            "To win, they must Connect4 tokens of their own either\n" .
            "horizontally, vertically or diagonally.\n" .
            "No one wins when neither player Connect's 4 token.\n" .
            "--------------------------------------\n" .
            "The players are...\n" .
            sprintf("Player One: Name %s, Token %s\n", $this->playerOne->getName(), $this->playerOne->getToken()) .
            sprintf("Player Two: Name %s, Token %s\n", $this->playerTwo->getName(), $this->playerTwo->getToken()) .
            Board::TOKEN_EMPTY_CELL . " indicates an empty cell and a valid drop point.\n" .
            "Press Ctrl+C anytime to exit the game.\n" .
            "Have fun!\n\n"
        );

        // Initialise the board and print it on the screen
        $this->getBoard()->init();
        $this->getBoard()->draw();

        // Pass the cells to MovesStore
        $this->getMovesStore()->setCells($this->getBoard()->getCells());

        // Remove any winner
        $this->setWinner(null);
    }

    /**
     * Start the game!
     */
    public function start()
    {
        $turn = 0;

        // While there's no winner or the maximum turns hasn't been reached
        while (!$this->getWinner() && $turn < $this->getMaximumTurns()) {
            if ($turn % 2) {
                // If mod is 1 or true
                // It's Player 2's turn
                $this->setCurrentPlayer($this->getPlayerTwo());
            } else {
                // It's Player 1's turn
                $this->setCurrentPlayer($this->getPlayerOne());
            }

            $validColumns = $this->getMovesStore()->getValidColumns();
            printInfo(sprintf('Please select from the column numbers %s', implode(', ', $validColumns)));

            $this->initiateMove();

            if ($this->getMovesStore()->checkWinningPatterns($this->getCurrentPlayer())) {
                $this->setWinner($this->getCurrentPlayer());
                // End the game since there's already a winner
                break;
            }

            $turn++;
        }

        if ($this->getWinner()) {
            // There's a winner!
            printSuccess("Congratulations! The winner is " . $this->getWinner()->getName());
        } else {
            printError("There is no winner. :(");
        }
    }

    /**
     * @param $maximumTurns
     */
    public function setMaximumTurns($maximumTurns)
    {
        $this->maximumTurns = $maximumTurns;
    }

    /**
     * Get the games maximum turns
     *
     * @return int
     */
    public function getMaximumTurns()
    {
        return $this->maximumTurns;
    }

    /**
     * Ask (human) or get (AI) the players desired move
     * @param int $tries
     */
    private function initiateMove($tries = 1)
    {
        $columnIndex = $this->getCurrentPlayer()->enterColumn() - 1;

        // This is just a cheap fix for memory exhaustion due to AIs
        // choosing of column that is already full multiple times.
        if (!$this->getCurrentPlayer()->isHuman() && $tries > 1000) {
            printError("Stalemate. There is no winner.");
            exit;
        }

        // Drop the token to the designated column
        if (!$this->getMovesStore()->dropToken($columnIndex, $this->getCurrentPlayer()->getToken())) {
            // Invalid dropping...
            if ($this->getCurrentPlayer()->isHuman()) {
                // Show only the errors to human and ignore error for robot
                printError($this->getMovesStore()->getError());
            }

            // Ask to make another move since there's an error
            $this->initiateMove($tries + 1);
        } else {
            // There's a valid move so we'll print that information
            $humanReadableColumn = $columnIndex + 1;
            printInfo(sprintf('%s %s move is in the column %s', $this->getCurrentPlayer()->getName(), $this->getCurrentPlayer()->getToken(), $humanReadableColumn));

            // Set the cells to the board and draw it
            $this->getBoard()->setCells($this->getMovesStore()->getCells());
            $this->getBoard()->draw();
        }
    }

    /**
     * @return PlayerInterface
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @param PlayerInterface $player
     */
    public function setWinner(PlayerInterface $player = null)
    {
        $this->winner = $player;
    }

    /**
     * @return PlayerInterface
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * @param PlayerInterface $currentPlayer
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;
    }

    /**
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param Board $board
     */
    public function setBoard($board)
    {
        $this->board = $board;
    }

    /**
     * @return PlayerInterface
     */
    public function getPlayerOne()
    {
        return $this->playerOne;
    }

    /**
     * @param PlayerInterface $playerOne
     */
    public function setPlayerOne($playerOne)
    {
        $this->playerOne = $playerOne;
    }

    /**
     * @return PlayerInterface
     */
    public function getPlayerTwo()
    {
        return $this->playerTwo;
    }

    /**
     * @param PlayerInterface $playerTwo
     */
    public function setPlayerTwo($playerTwo)
    {
        $this->playerTwo = $playerTwo;
    }

    /**
     * @return MovesStore
     */
    public function getMovesStore()
    {
        return $this->movesStore;
    }

    /**
     * @param MovesStore $movesStore
     */
    public function setMovesStore($movesStore)
    {
        $this->movesStore = $movesStore;
    }
}