<?php

namespace Connect4\Player\AiPlayer;


use Connect4\Player\PlayerAbstract;


/**
 * Class SmarterAiPlayer
 *
 * @package Connect4\Player
 */
class SmarterAiPlayer extends PlayerAbstract implements AiPlayerInterface
{
    const IS_HUMAN = false;

    public function __construct()
    {
        $this->setHumanStatus(self::IS_HUMAN);
    }

    /**
     * Randomly return a number from valid columns
     * @return int
     */
    public function enterColumn()
    {
        // When we have the AI logic for selecting the best column,
        // we can use the code below. For now it just selects from
        // valid columns
        return $this->smartColumnSelection();
    }

    /**
     * @inheritdoc
     */
    public function smartColumnSelection()
    {
        // Select from valid columns
        $validColumns = $this->getMovesStore()->getValidColumns();

        return array_rand($validColumns, 1);
    }
}