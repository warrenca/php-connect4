<?php

namespace Connect4\Player;


class DumbAiPlayer implements Player
{
    const IS_HUMAN = false;

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    public function setToken($token)
    {
        // TODO: Implement setToken() method.
    }

    public function getToken()
    {
        // TODO: Implement getToken() method.
    }

    public function dropToken($position)
    {
        // TODO: Implement dropToken() method.
    }

    public function isHuman()
    {
        return self::IS_HUMAN;
    }

    public function enterPosition()
    {
        return 'R1C1';
    }
}