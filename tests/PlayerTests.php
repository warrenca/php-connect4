<?php

namespace Connect4\Tests;


use Connect4\Player\DumbAiPlayer;
use Connect4\Player\HumanPlayer;
use Connect4\Player\Player;
use Connect4\Store\MovesStore;
use Connect4\View\Board;
use PHPUnit\Framework\TestCase;

class PlayerTests extends TestCase
{
    const NAME = "DumpAI";

    /** @var Player */
    private $player;


    public function setup()
    {
        $this->player = new DumbAiPlayer();
    }

    /**
     * @dataProvider nameProvider
     * @param $name
     */
    public function testMustSetAndGetName($name)
    {
        $this->player->setName($name);

        self::assertEquals(self::NAME, $this->player->getName());
    }

    public function nameProvider()
    {
        return [
            [self::NAME]
        ];
    }

    public function testMustSetAndGetToken()
    {
        $this->player->setToken(Board::TOKEN_PLAYER_ONE);

        self::assertEquals(Board::TOKEN_PLAYER_ONE, $this->player->getToken());
    }

    public function testMustBeHumanOrAI()
    {
        $this->player->setHumanStatus(true);
        self::assertTrue($this->player->isHuman());

        $this->player->setHumanStatus(false);
        self::assertFalse($this->player->isHuman());
    }

    public function testMustSetAndGetMovesStore()
    {
        $movesStore = new MovesStore();
        $this->player->setMovesStore($movesStore);

        self::assertInstanceOf('Connect4\\Store\\MovesStore', $this->player->getMovesStore());
    }

//    public function testInputtedColumnMustMatch()
//    {
//        $humanPlayer = new HumanPlayer();
//        $humanPlayer->setToken(Board::TOKEN_PLAYER_TWO);
//        $humanPlayer->setName('Human');
//
//        $humanPlayer->enterColumn();
//    }
}