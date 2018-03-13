<?php

namespace Connect4\Player\AiPlayer;


interface AiPlayerInterface
{
    public function enterColumn();

    public function smartColumnSelection();
}