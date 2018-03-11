<?php

namespace Connect4\Store;


class MovesStore
{

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

    public function dropToken($position, $token)
    {
        $positions = $this->parsePosition($position);

        // validate position
        $this->cells[$positions['row']][$positions['column']] = $token;
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

        $positions['column']--;
        $positions['row']--;

        return $positions;
    }
}