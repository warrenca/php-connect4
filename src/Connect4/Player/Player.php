<?php

namespace Connect4\Player;


interface Player
{
    public function getName();

    public function setName($name);

    public function setToken($token);

    public function getToken();

    public function dropToken($position);

    public function isHuman();

    public function enterPosition();
}