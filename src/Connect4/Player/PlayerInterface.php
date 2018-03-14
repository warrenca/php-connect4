<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

/**
 * Interface Player
 * Core methods for a Player to play the Connect4
 *
 * @package Connect4\Player
 */
interface PlayerInterface
{
    /**
     * Get the players name
     * @return mixed
     */
    public function getName();

    /**
     * Set the players name
     * @param $name
     */
    public function setName($name);

    /**
     * Set the token for the player
     * @param $token
     */
    public function setToken($token);

    /**
     * Get the token for the player
     * @return mixed
     */
    public function getToken();

    /**
     * Get the status if the player is a human
     * @return mixed
     */
    public function isHuman();

    /**
     * A way to input and return the desired column number
     * @return mixed
     */
    public function enterColumn();

    /**
     * Set the MovesStore
     * @param MovesStore $movesStore
     */
    public function setMovesStore(MovesStore $movesStore);

    /**
     * Get the MoveStore
     * @return mixed
     */
    public function getMovesStore();

    /**
     * Sets the human status
     * @param bool $humanStatus
     */
    public function setHumanStatus($humanStatus);
}