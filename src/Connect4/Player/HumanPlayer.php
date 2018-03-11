<?php

namespace Connect4\Player;


class HumanPlayer implements Player
{
    const IS_HUMAN = true;

    private $token;

    private $name;

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

    public function dropToken($position)
    {
        // TODO: Implement dropToken() method.
    }

    public function enterPosition()
    {
        return readline(sprintf("\n%s Position: ", $this->getName()));
    }
}