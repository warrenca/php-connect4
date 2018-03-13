<?php

namespace Connect4\Tests;


use Connect4\Game;
use Connect4\View\Board;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class GameTests extends TestCase
{
    /** @var Game */
    private $game;
    private $container;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setup()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('./php-di/config.php');

        $this->container = $builder->build();
        $this->game = $this->container->get('connect4.gameTest');
    }

    /** Must get the MovesStore instance */
    public function testMustGetMoveStore()
    {
        self::assertInstanceOf("Connect4\\Store\\MovesStore", $this->game->getMovesStore());
    }

    /** Must get the Board instance */
    public function testMustGetBoard()
    {
        self::assertInstanceOf("Connect4\\View\\Board", $this->game->getBoard());
    }

    /** Must get the PlayerOne instance */
    public function testMustGetPlayerOne()
    {
        self::assertInstanceOf("Connect4\\Player\\DumbAiPlayer", $this->game->getPlayerOne());
    }

    /** Must not get playerOne as an instance of HumanPlayer */
    public function testMustNotGetPlayerOne()
    {
        self::assertNotInstanceOf("Connect4\\Player\\HumanPlayer", $this->game->getPlayerOne());
    }

    /** Must get players instance as PlayerAbstract */
    public function testMustGetPlayersAsPlayerAbstract()
    {
        self::assertInstanceOf("Connect4\\Player\\PlayerAbstract", $this->game->getPlayerOne());
        self::assertInstanceOf("Connect4\\Player\\PlayerAbstract", $this->game->getPlayerTwo());
    }

    /** Must get and set the current player of the game */
    public function testMustSetAndGetCurrentPlayer()
    {
        $this->game->setCurrentPlayer($this->game->getPlayerTwo());
        self::assertInstanceOf("Connect4\\Player\\DumbAiPlayer", $this->game->getCurrentPlayer());
        self::assertEquals("Robot 2 🤖", $this->game->getCurrentPlayer()->getName());
    }

    /** Must get and set the winning player of the game */
    public function testMustSetAndGetWinner()
    {
        $this->game->setWinner($this->game->getPlayerOne());
        self::assertInstanceOf("Connect4\\Player\\DumbAiPlayer", $this->game->getWinner());
        self::assertEquals("Robot 🤖", $this->game->getWinner()->getName());
    }

    /** Must return the correct maximum turns */
    public function testMustReturnMaximumTurns()
    {
        self::assertEquals(Board::ROWS * Board::COLUMNS, $this->game->getMaximumTurns());
    }

    /**
     * Game relates settings must match what was defined in the setup
     * Look for the definition here $this->container->get('connect4.gameTest')
     */
    public function testMustMatchDefinedSetup()
    {
        // Hide the console output
        $this->setOutputCallback(function() {});
        $this->game->setup();
        self::assertEquals($this->container->get('connect4.player.ai'), $this->game->getPlayerOne());
        self::assertEquals($this->container->get('connect4.player.ai2'), $this->game->getPlayerTwo());
        self::assertEquals($this->game->getMovesStore()->getCells(), $this->game->getBoard()->getCells());
    }

    /** When the game ends, we must return a winner */
    public function testMustReturnAWinner()
    {
        $this->setOutputCallback(function() {});
        $this->game->setup();
        $this->game->setWinner($this->game->getPlayerOne());
        ob_start();
        $this->setOutputCallback(function() {});
        $this->game->start();
        $contents = ob_get_contents();
        ob_end_clean();

        self::assertContains("Congratulations! The winner is Robot 🤖", $contents);
    }

    /** When the game ends, we must return no winner */
    public function testMustReturnNoWinner()
    {
        $this->setOutputCallback(function() {});
        $this->game->setup();
        $this->game->setWinner(null);
        $this->game->setMaximumTurns(0);
        ob_start();
        $this->setOutputCallback(function() {});
        $this->game->start();
        $contents = ob_get_contents();
        ob_end_clean();

        self::assertContains("There is no winner. :(", $contents);
    }

}