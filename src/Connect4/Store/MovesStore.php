<?php

namespace Connect4\Store;


use Connect4\Player\Player;
use Connect4\View\Board;

/**
 * Class MovesStore
 * This class stores all the moves, validates moves and check moves for winning patterns
 *
 * @package Connect4\Store
 */
class MovesStore
{
    const NUMBER_OF_TOKENS_TO_WIN = 4;

    private $cells = [];
    private $error = "";

    /**
     * @return array
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @param array $cells
     */
    public function setCells($cells)
    {
        $this->cells = $cells;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the error message
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Initiate token dropping in a column
     *
     * @param $columnIndex
     * @param $token
     * @return bool
     */
    public function dropToken($columnIndex, $token)
    {
        if (!$this->isColumnInRange($columnIndex))
        {
            return false;
        }

        $rowIndex = $this->getNextAvailableRowIndex($columnIndex);

        if ($this->isColumnFull($rowIndex, $columnIndex))
        {
            return false;
        }

        // No problemo! Let's set the token in the cells array
        $cells = $this->getCells();
        $cells[$rowIndex][$columnIndex] = $token;
        $this->setCells($cells);

        return true;
    }

    /**
     * Check if a column is full
     *
     * @param $rowIndex
     * @param $columnIndex
     * @return bool
     */
    private function isColumnFull($rowIndex, $columnIndex)
    {
        // Empty up the error before the validations
        $this->setError("");

        if ($rowIndex < 0)
        {
            $this->setError(sprintf('Column %d is already full, please choose a different column.', ($columnIndex + 1)));
            return true;
        }

        return false;
    }

    /**
     * Get the array index of the next available row
     * -1 means there's no more available slot for that column
     *
     * @param $columnIndex
     * @return int
     */
    private function getNextAvailableRowIndex($columnIndex)
    {
        $cells = $this->getCells();

        for ($rowIndex = (Board::ROWS - 1); $rowIndex >= 0; $rowIndex--)
        {
            if ($cells[$rowIndex][$columnIndex] === Board::TOKEN_EMPTY_CELL)
            {
                return $rowIndex;
            }
        }

        return -1;
    }

    /**
     * Check whether the selected column is in range
     *
     * @param $columnIndex
     * @return bool
     */
    private function isColumnInRange($columnIndex)
    {
        $this->setError("");
        if ($columnIndex < 0 || $columnIndex > (Board::COLUMNS - 1))
        {
            $this->setError('Invalid column, please only choose from '. implode(', ', range(1, Board::COLUMNS)));
            return false;
        }

        return true;
    }

    /**
     * Check if there's any winner based on some patterns
     *
     * @param Player $player
     * @return null|string
     */
    public function checkWinningPatterns(Player $player)
    {
        $token = $player->getToken();

        return $this->checkHorizontalWinningPattern($token)
            || $this->checkVerticalWinningPattern($token)
            || $this->checkFromLowerLeftToUpperRightWinningPattern($token)
            || $this->checkFromUpperLeftToLowerRightWinningPattern($token);
    }

    /**
     * Check if there are X number of consecutive tokens horizontally
     *
     * @param $token
     * @return bool
     */
    public function checkHorizontalWinningPattern($token)
    {
        $cells = $this->getCells();

        // The highest index where winning is possible
        $limit = Board::COLUMNS - (self::NUMBER_OF_TOKENS_TO_WIN -1);

        for ($rowIndex = (Board::ROWS - 1); $rowIndex > 0; $rowIndex--)
        {
            for ($columnIndex = 0; $columnIndex < $limit; $columnIndex++)
            {
                if ($cells[$rowIndex][$columnIndex] === $token
                    && $cells[$rowIndex][$columnIndex+1] === $token
                    && $cells[$rowIndex][$columnIndex+2] === $token
                    && $cells[$rowIndex][$columnIndex+3] === $token
                )
                {
                    return true;
                }
            }
        }
    }

    /**
     * Check for vertical winning pattern
     *
     * @param $token
     * @return bool
     */
    private function checkVerticalWinningPattern($token)
    {
        $cells = $this->getCells();

        // The highest index where a winning pattern is possible
        $limit = (self::NUMBER_OF_TOKENS_TO_WIN - 1);

        for ($columnIndex = 0; $columnIndex < Board::COLUMNS; $columnIndex++)
        {
            for ($rowIndex = (Board::ROWS - 1); $rowIndex >= $limit; $rowIndex--)
            {
                if ($cells[$rowIndex][$columnIndex] === $token
                    && $cells[$rowIndex - 1][$columnIndex] === $token
                    && $cells[$rowIndex - 2][$columnIndex] === $token
                    && $cells[$rowIndex - 3][$columnIndex] === $token
                ) {
                    return true;
                }
            }
        }
    }

    /**
     * Check diagonal winning pattern from lower left to upper right
     *
     * @param $token
     * @return bool
     */
    private function checkFromLowerLeftToUpperRightWinningPattern($token)
    {
        $cells = $this->getCells();

        // Column start index where it's possible to win
        $start = (Board::COLUMNS - self::NUMBER_OF_TOKENS_TO_WIN);

        // Row limit index where it's possible to win
        $limit = (Board::ROWS - self::NUMBER_OF_TOKENS_TO_WIN - 1);
        for ($columnIndex = $start; $columnIndex < Board::COLUMNS; $columnIndex++)
        {
            for ($rowIndex = 0; $rowIndex < $limit; $rowIndex++)
            {
                if ($cells[$rowIndex][$columnIndex] === $token
                    && $cells[$rowIndex+1][$columnIndex-1] === $token
                    && $cells[$rowIndex+2][$columnIndex-2] === $token
                    && $cells[$rowIndex+3][$columnIndex-3] === $token
                    )
                {
                    return true;
                }
            }
        }
    }

    /**
     * Check diagonal winning pattern from upper left to lower right
     *
     * @param $token
     * @return bool
     */
    private function checkFromUpperLeftToLowerRightWinningPattern($token)
    {
        $cells = $this->getCells();

        // Column start index where it's possible to win
        $columnStart = (Board::COLUMNS - (self::NUMBER_OF_TOKENS_TO_WIN - 1));

        // Row start index where it's possible to win
        $rowStart = (Board::ROWS - (self::NUMBER_OF_TOKENS_TO_WIN - 1));

        for ($columnIndex = $columnStart; $columnIndex < Board::COLUMNS; $columnIndex++)
        {
            for ($rowIndex = $rowStart; $rowIndex < Board::ROWS; $rowIndex++)
            {
                if ($cells[$rowIndex][$columnIndex] === $token
                    && $cells[$rowIndex-1][$columnIndex-1] === $token
                    && $cells[$rowIndex-2][$columnIndex-2] === $token
                    && $cells[$rowIndex-3][$columnIndex-3] === $token
                )
                {
                    return true;
                }
            }
        }
    }
}