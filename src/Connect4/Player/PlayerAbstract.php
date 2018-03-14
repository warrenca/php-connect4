<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

/**
 * Class PlayerAbstract
 * Defines most of the methods and properties for a player
 *
 * @package Connect4\Player
 */
abstract class PlayerAbstract implements PlayerInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $name;

    /** @var MovesStore */
    private $movesStore;

    /** @var bool */
    private $humanStatus;

    /**
     * @inheritdoc
     */
    public function isHuman()
    {
        return $this->humanStatus;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function getMovesStore()
    {
        return $this->movesStore;
    }

    /**
     * @inheritdoc
     */
    public function setMovesStore(MovesStore $movesStore)
    {
        $this->movesStore = $movesStore;
    }

    /**
     * @inheritdoc
     */
    public function setHumanStatus($humanStatus)
    {
        $this->humanStatus = $humanStatus;
    }
}