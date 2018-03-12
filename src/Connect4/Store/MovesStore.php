<?php

namespace Connect4\Store;


use Connect4\Player\Player;
use Connect4\View\Board;

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

    public function dropToken($position, $token)
    {
        $positions = $this->parsePosition($position);
        $cells = $this->getCells();

        // validate position
        if (!$this->validateMove($positions))
        {
            return false;
        }

        $cells[$positions['row']][$positions['column']] = $token;
        $this->setCells($cells);

        return true;
    }

    private function validateMove($positions)
    {
        // Empty up the error before the validations
        $this->setError("");

        $cells = $this->getCells();
        $cellValue = $cells[$positions['row']][$positions['column']];
        $rowBelow = $positions['row'] + 1;
        $cellValueBelow = null;

        if ($rowBelow < count($this->getCells()))
        {
            $cells = $this->getCells();
            $cellValueBelow = $cells[$rowBelow][$positions['column']];
        }

        if ($cellValue !== Board::TOKEN_EMPTY_CELL)
        {
            // The cell is occupied
            $this->setError("The cell is already occupied.");
            return false;
        } else if ($cellValueBelow === Board::TOKEN_EMPTY_CELL)
        {
            $this->setError("Invalid position, there's nothing under the token.");
            return false;
        }

        return true;
    }

    private function parsePosition($position)
    {
        // Split the position by comma
        $positionArray = explode(',', strtolower($position));

        // Set the initial row/column values
        $positions = ['row' => null, 'column' => null];

        // Check the Column number inputted
        if (($CPosition = strpos($positionArray[0], 'c')) !== false)
        {
            $positions['column'] = substr($positionArray[0], $CPosition + 1);
        } else if (($CPosition = strpos($positionArray[1], 'c')) !== false)
        {
            $positions['column'] = substr($positionArray[1], $CPosition + 1);
        }

        // Check the Row number inputted
        if (($RPosition = strpos($positionArray[0], 'r')) !== false)
        {
            $positions['row'] = substr($positionArray[0], $RPosition + 1);
        } else if (($RPosition = strpos($positionArray[1], 'r')) !== false)
        {
            $positions['row'] = substr($positionArray[1], $RPosition + 1);
        }

        // Apply minus 1 for each because these are array index
        $positions['column']--;
        $positions['row']--;

        return $positions;
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
     * Check if there's any winner and return the token value of the winner
     * @param Player $player
     * @return null|string
     */
    public function checkWinner(Player $player)
    {
        // Check each row if there's a winner horizontally
        $cells = $this->getCells();

        return $this->horizontalWinningPattern($cells, $player->getToken());
    }

    public function horizontalWinningPattern($cells, $token)
    {
        for ($row = (Board::ROWS - 1); $row > 0; $row--)
        {
            for ($column = 0; $column < (Board::COLUMNS - self::NUMBER_OF_TOKENS_TO_WIN); $column++)
            {
                $rowValues = $cells[$row];
                $rowValues = array_slice($rowValues, $column, self::NUMBER_OF_TOKENS_TO_WIN);

                if ($this->areAllTokensIdentical($rowValues, $token))
                {
                    return true;
                }
            }
        }
    }

    /**
     * Check if all tokens are identical and return the token value
     *
     * @param array $rowValues
     * @param $token
     * @return bool
     */
    public function areAllTokensIdentical(array $rowValues, $token)
    {
        $tokens = implode("", $rowValues);

        return $tokens === str_repeat($token, self::NUMBER_OF_TOKENS_TO_WIN);
    }
}