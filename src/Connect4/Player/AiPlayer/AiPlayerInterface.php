<?php

namespace Connect4\Player\AiPlayer;


interface AiPlayerInterface
{
    /**
     * @return mixed
     */
    public function smartColumnSelection();
}