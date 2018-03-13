<?php

namespace Connect4\Player\AiPlayer;


use Connect4\Player\PlayerAbstract;
use Connect4\View\Board;

/**
 * Class DumbAiPlayer
 *
 * @package Connect4\Player
 */
class DumbAiPlayer extends PlayerAbstract implements AiPlayerInterface
{
    const IS_HUMAN = false;

    public function __construct()
    {
        $this->setHumanStatus(self::IS_HUMAN);
    }

    /**
     * Randomly return a number from 1 to the maximum board column
     * @return int
     */
    public function enterColumn()
    {
        return rand(1, Board::COLUMNS);
        // When we have the AI logic for selecting the best column,
        // we can use the code below
        // return $this->smartColumnSelection();
    }

    public function smartColumnSelection()
    {
        // If we want a smart column selection that will block an opponents move
        // and will try to win, we can do it here.
        // We can use the $this->getMovesStore() to analyse this AIs and opponents token positions
        // then finally return the best column number.
    }
}