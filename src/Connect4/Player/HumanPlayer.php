<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

class HumanPlayer implements Player
{
    const IS_HUMAN = true;

    private $token;

    private $name;

    private $movesStore;

    public function isHuman()
    {
        return self::IS_HUMAN;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

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

    public function enterPosition()
    {
        return readline(sprintf("Enter %s Position: ", $this->getName()));
    }
}