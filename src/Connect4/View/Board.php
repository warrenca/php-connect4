<?php

namespace Connect4\View;


class Board
{
    const TOKEN_EMPTY_CELL = '[ ]';
    const TOKEN_PLAYER_ONE = '[X]';
    const TOKEN_PLAYER_TWO = '[O]';

    const ROWS = 6;
    const COLUMNS = 7;

    private $cells = [];

    public function init()
    {
        for ($row = 0; $row < self::ROWS; $row++)
        {
            for ($column = 0; $column < self::COLUMNS; $column++)
            {
                $this->cells[$row][$column] = self::TOKEN_EMPTY_CELL;
            }
        }

        return $this;
    }

    public function setCells($cells)
    {
        $this->cells = $cells;
    }

    public function draw()
    {
        echo "R↓\n";

        for ($rowIndex = 0; $rowIndex < self::ROWS; $rowIndex++)
        {
            echo ($rowIndex + 1) . " ";
            for ($columnIndex = 0; $columnIndex < count($this->cells[$rowIndex]); $columnIndex++)
            {
                echo $this->cells[$rowIndex][$columnIndex];
            }
            echo "\n";
        }

        echo "   ";
        for ($columnIndex = 0; $columnIndex < self::COLUMNS; $columnIndex++)
        {
            echo ($columnIndex + 1) . "  ";
        }

        echo "←C";

        echo "\n\n";
    }

    public function getCells()
    {
        return $this->cells;
    }
}