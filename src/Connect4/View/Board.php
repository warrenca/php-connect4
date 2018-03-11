<?php

namespace Connect4\View;


class Board
{
    private $rows;
    private $columns;
    private $cells = [];

    public function __construct($rows, $columns)
    {
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function init()
    {
        for ($row = 0; $row < $this->rows; $row++)
        {
            for ($column = 0; $column < $this->columns; $column++)
            {
                $this->cells[$row][$column] = "[ ]";
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
        for ($rowIndex = 0; $rowIndex < count($this->cells); $rowIndex++)
        {
            echo ($rowIndex + 1) . " ";
            for ($columnIndex = 0; $columnIndex < count($this->cells[$rowIndex]); $columnIndex++)
            {
                echo $this->cells[$rowIndex][$columnIndex];
            }
            echo "\n";
        }

        echo "   ";
        for ($columnIndex = 0; $columnIndex < $this->columns; $columnIndex++)
        {
            echo ($columnIndex + 1) . "  ";
        }

        echo "\n\n";
    }

    public function getCells()
    {
        return $this->cells;
    }
}