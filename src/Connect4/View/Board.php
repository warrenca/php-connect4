<?php

namespace Connect4\View;
use Connect4\CellsTrait;

/**
 * Class Board
 * This class provides the interface to display the Connect4 board on the screen
 *
 * @package Connect4\View
 */
class Board
{
    use CellsTrait;

    /** A representation of an empty cell */
    const TOKEN_EMPTY_CELL = '[ ]';

    /** A representation of player one's occupied cell */
    const TOKEN_PLAYER_ONE = '[X]';

    /** A representation of player two's occupied cell */
    const TOKEN_PLAYER_TWO = '[O]';

    /** The number of board row */
    const ROWS = 6;

    /** The number of board column */
    const COLUMNS = 7;

    /**
     * Initialise the board with empty cells
     *
     * @return void
     */
    public function init()
    {
        $cells = [];

        for ($row = 0; $row < self::ROWS; $row++) {
            for ($column = 0; $column < self::COLUMNS; $column++) {
                $cells[$row][$column] = self::TOKEN_EMPTY_CELL;
            }
        }

        $this->setCells($cells);
    }

    /**
     * Draw in the console the moves already taken
     * @param bool $printCanvas
     * @return string
     */
    public function draw($printCanvas = true)
    {
        $cells = $this->getCells();
        $canvas = "";

        // Looping through the cells and display it
        for ($rowIndex = 0; $rowIndex < self::ROWS; $rowIndex++) {
            $canvas .= "    ";
            for ($columnIndex = 0; $columnIndex < count($this->cells[$rowIndex]); $columnIndex++) {
                $canvas .= $cells[$rowIndex][$columnIndex];
            }
            $canvas .= "\n";
        }

        // Put a label under the board
        $canvas .= "C->";
        for ($columnIndex = 0; $columnIndex < self::COLUMNS; $columnIndex++) {
            $canvas .= "  " . ($columnIndex + 1);
        }

        $canvas .= "\n\n";

        if ($printCanvas) {
            echo $canvas;
        } else {
            return $canvas;
        }
    }
}