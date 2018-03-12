<?php

namespace Connect4\Player;


use Connect4\Store\MovesStore;

interface Player
{
    public function getName();

    public function setName($name);

    public function setToken($token);

    public function getToken();

    public function isHuman();

    public function enterColumn();

    public function setMovesStore(MovesStore $board);

    public function getMovesStore();
}