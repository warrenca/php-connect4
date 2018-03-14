<?php

namespace Connect4\Tests;


use Connect4\Player\AiPlayer\DumbAiPlayer;
use Connect4\Player\PlayerInterface;
use Connect4\Store\MovesStore;
use Connect4\View\Board;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class PlayerTests extends TestCase
{
    const NAME = "DumbAI";

    /** @var PlayerInterface */
    private $player;

    /** @var Container */
    private $container;


    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function setup()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('./php-di/config.php');
        $this->container = $builder->build();

        $this->player = $this->container->get('connect4.player.ai');
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

    /** Must identify the player if human and ai */
    public function testMustBeAHumanOrAi()
    {
        $this->player->setHumanStatus(true);
        self::assertTrue($this->player->isHuman());

        $this->player->setHumanStatus(false);
        self::assertFalse($this->player->isHuman());
    }

    /**
     * Must get and set move store
     */
    public function testMustSetAndGetMovesStoreClass()
    {
        $movesStore = $this->container->get('connect4.store.movesStore');
        $this->player->setMovesStore($movesStore);

        self::assertInstanceOf(MovesStore::class, $this->player->getMovesStore());
    }

    /** Must have a column entered by the user that is within range */
    public function testMustHaveSelectedAColumnWithinRange()
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