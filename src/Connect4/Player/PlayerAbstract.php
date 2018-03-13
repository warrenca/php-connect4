<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

/**
 * Class PlayerAbstract
 * Defines most of the methods and properties for a player
 *
 * @package Connect4\Player
 */
abstract class PlayerAbstract implements Player
{
    private $token;

    private $name;

    private $movesStore;

    private $humanStatus;

    /**
     * Tells if the player is human
     *
     * @return bool
     */
    public function isHuman()
    {
        return $this->humanStatus;
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

    public function setHumanStatus($humanStatus)
    {
        $this->humanStatus = $humanStatus;
    }
}