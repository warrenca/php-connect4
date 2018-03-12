<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;
use Connect4\View\Board;

class DumbAiPlayer implements Player
{
    const IS_HUMAN = false;

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

    private function smartColumnSelection()
    {
        // If we want a smart column selection that will block an opponents move
        // and will try to win, we can do it here.
        // We can use the $this->getMovesStore() to analyse this AIs and opponents token positions
        // then finally return the best column number.
    }
}