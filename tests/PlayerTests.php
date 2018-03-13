<?php

namespace Connect4\Tests;


use Connect4\Player\AiPlayer\DumbAiPlayer;
use Connect4\Player\PlayerInterface;
use Connect4\Store\MovesStore;
use Connect4\View\Board;
use PHPUnit\Framework\TestCase;

class PlayerTests extends TestCase
{
    const NAME = "DumbAI";

    /** @var PlayerInterface */
    private $player;


    public function setup()
    {
        $this->player = new DumbAiPlayer();
    }

    /**
     * Must get and set the players name
     * @dataProvider nameProvider
     * @param $name
     */
    public function testMustSetAndGetName($name)
    {
        $this->player->setName($name);

        self::assertEquals(self::NAME, $this->player->getName());
    }

    /** Must get and set the correct token */
    public function testMustSetAndGetToken()
    {
        $this->player->setToken(Board::TOKEN_PLAYER_ONE);

        self::assertEquals(Board::TOKEN_PLAYER_ONE, $this->player->getToken());
    }

    /** Must identify the player if human and robot */
    public function testMustBeHumanOrAI()
    {
        $this->player->setHumanStatus(true);
        self::assertTrue($this->player->isHuman());

        $this->player->setHumanStatus(false);
        self::assertFalse($this->player->isHuman());
    }

    /** Must get and set move store */
    public function testMustSetAndGetMovesStore()
    {
        $movesStore = new MovesStore();
        $this->player->setMovesStore($movesStore);

        self::assertInstanceOf(MovesStore::class, $this->player->getMovesStore());
    }

    /** Must check that the column entered by the user is within range */
    public function testMustHaveAColumnWithinRange()
    {
        $column = $this->player->enterColumn();

        self::assertTrue(in_array($column, range(1, Board::COLUMNS)));
    }

    public function nameProvider()
    {
        return [
            [self::NAME]
        ];
    }
}