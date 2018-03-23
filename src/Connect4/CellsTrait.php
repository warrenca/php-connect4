<?php

namespace Connect4;


trait CellsTrait
{
    /**
     * Contains all the information about the token positions in the board
     * @var array
     */
    private $cells = [];

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
}