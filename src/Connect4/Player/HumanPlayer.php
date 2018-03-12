<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

class HumanPlayer implements Player
{
    const IS_HUMAN = true;

    private $token;

    private $name;

    private $movesStore;

    /**
     * Tells if the player is human
     *
     * @return bool
     */
    public function isHuman()
    {
        return self::IS_HUMAN;
    }

    /**
     * Sets the name of the player
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the token for the player
     *
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return MovesStore
     */
    public function getMovesStore()
    {
        return $this->movesStore;
    }

    /**
     * @param MovesStore $movesStore
     */
    public function setMovesStore(MovesStore $movesStore)
    {
        $this->movesStore = $movesStore;
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