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
    const TIME_PER_MOVE_IN_SECONDS = 15;

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
        printInfo(sprintf("%s%s) Enter a column number: ", $this->getName(), $this->getToken()), false);
        return $this->readlineTimeout(self::TIME_PER_MOVE_IN_SECONDS, "");
    }

    /**
     * Timeout for readline, only works in linux
     * http://php.net/manual/en/function.readline.php#91643
     *
     * @param $sec
     * @param $def
     * @return string
     */
    public function readlineTimeout($sec, $def)
    {
        return trim(shell_exec('bash -c ' .
            escapeshellarg('phprlto=' .
                escapeshellarg($def) . ';' .
                'read -t ' . ((int)$sec) . ' phprlto;' .
                'echo "$phprlto"')));
    }
}