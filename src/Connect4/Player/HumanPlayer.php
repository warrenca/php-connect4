<?php

namespace Connect4\Player;


/**
 * Class HumanPlayer
 *
 * @package Connect4\Player
 */
class HumanPlayer extends PlayerAbstract
{
    const IS_HUMAN = true;

    public function __construct()
    {
        $this->setHumanStatus(self::IS_HUMAN);
    }

    /**
     * Ask the human to enter the column number where they want to drop the token
     *
     * @return string
     */
    public function enterColumn()
    {
        return readline(sprintf("%s%s) Enter a column number: ", $this->getName(), $this->getToken()));
    }
}