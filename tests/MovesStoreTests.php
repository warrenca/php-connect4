<?php

namespace Connect4\Tests;


use Connect4\Player\HumanPlayer;
use Connect4\Player\PlayerAbstract;
use Connect4\Store\MovesStore;
use Connect4\View\Board;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class MovesStoreTests extends TestCase
{

    /** @var  MovesStore */
    private $movesStore;

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

        $this->movesStore = $this->container->get('connect4.store.movesStore');;
    }

    /**
     * ALl constants must match based on what was defined in the Board class
     */
    public function testConstantsShouldBeAsDefined()
    {
        self::assertEquals(4, MovesStore::NUMBER_OF_TOKENS_TO_WIN);
    }

    /**
     * Must set and get correct cells
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustSetAndGetCells($cells)
    {
        $this->movesStore->setCells($cells);
        self::assertEquals($cells, $this->movesStore->getCells());
    }

    /** Must get and set correct errors */
    public function testMustSetAndGetError()
    {
        $error = "This is an error";
        $this->movesStore->setError($error);

        self::assertEquals($error, $this->movesStore->getError());
    }

    /**
     * Must return true if the column is full
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustReturnTrueIfColumnIsFull($cells)
    {
        $this->movesStore->setCells($cells);

        $columnIndex = 1;
        $nextRowIndex = $this->movesStore->getNextAvailableRowIndex($columnIndex);
        // Column is full
        self::assertEquals(-1, $nextRowIndex);

        self::assertTrue($this->movesStore->isColumnFull($nextRowIndex, $columnIndex));
        self::assertEquals("Column 2 is already full, please choose a different column.", $this->movesStore->getError());
    }

    /**
     * Must omit column 2 since it's already full
     *
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustReturnValidColumns($cells)
    {
        $this->movesStore->setCells($cells);
        $validColumns = implode(', ', $this->movesStore->getValidColumns());
        $this->assertEquals('1, 3, 4, 5, 6, 7', $validColumns);
    }

    /** Must return false if the column is not full */
    public function testMustReturnFalseIfColumnIsNotFull()
    {
        self::assertFalse($this->movesStore->isColumnFull(0, 1));
    }

    /** Must return true if the selected column is in range */
    public function testMustReturnTrueIfColumnIsInRange()
    {
        array_map(function($column)
        {
            self::assertTrue($this->movesStore->isColumnInRange($column-1));
        }, range(1, Board::COLUMNS));
    }

    /**
     * Must return false when dropping a token to an incorrect column
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustReturnFalseWhenDroppingToIncorrectColumnIndex($cells)
    {
        $this->movesStore->setCells($cells);

        // Column is already full
        self::assertFalse($this->movesStore->dropToken(1, Board::TOKEN_PLAYER_TWO));
        self::assertEquals("Column 2 is already full, please choose a different column.", $this->movesStore->getError());

        // Column out of range
        self::assertFalse($this->movesStore->dropToken(Board::COLUMNS, Board::TOKEN_PLAYER_TWO));

        // Column 2 is full, so don't suggest it
        self::assertEquals("Invalid column, please only choose from 1, 3, 4, 5, 6, 7", $this->movesStore->getError());
    }

    /**
     * Must return true when dropping a token to a correct column
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustReturnTrueWhenDroppingToCorrectColumnIndex($cells)
    {
        $this->movesStore->setCells($cells);

        self::assertTrue($this->movesStore->dropToken(0, Board::TOKEN_PLAYER_TWO));
    }

    /** Must return false if the column is not in range */
    /**
     * @dataProvider cellProvider1
     */
    public function testMustReturnFalseIfColumnIsNotInRange($cells)
    {
        $this->movesStore->setCells($cells);
        self::assertFalse($this->movesStore->isColumnInRange(Board::COLUMNS));

        // Column 2 is full, so don't suggest it
        self::assertEquals("Invalid column, please only choose from 1, 3, 4, 5, 6, 7", $this->movesStore->getError());
    }

    /**
     * Must get the correct row index based on the cells or existing moves
     * @dataProvider cellProvider1
     * @param $cells
     */
    public function testMustGetTheCorrectRowIndex($cells)
    {
        $this->movesStore->setCells($cells);

        // Column is full
        self::assertEquals(-1, $this->movesStore->getNextAvailableRowIndex(1));
        // Fillable column
        self::assertEquals(4, $this->movesStore->getNextAvailableRowIndex(0));
    }

    /**
     * Test horizontal winning patterns
     *
     * @param $pattern
     * @dataProvider horizontalWinningPatternProviders
     */
    public function testMustReturnTrueForHorizontalWinningPatterns($pattern)
    {
        /** @var PlayerAbstract $playerOne */
        $playerOne = $this->container->get('connect4.player.human');
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        // Return true for horizontal winning patterns
        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkHorizontalPattern(Board::TOKEN_PLAYER_ONE));

        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));
    }

    /**
     * Test vertical winning patterns
     *
     * @param $pattern
     * @dataProvider verticalWinningPatternProviders
     */
    public function testMustReturnTrueForVerticalWinningPatterns($pattern)
    {
        /** @var PlayerAbstract $playerOne */
        $playerOne = $this->container->get('connect4.player.human');
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        // Return true for horizontal winning patterns
        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkVerticalPattern(Board::TOKEN_PLAYER_ONE));

        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));
    }

    /**
     * Test forward slash (/) winning patterns
     *
     * @param $pattern
     * @dataProvider forwardSlashWinningPatternProviders
     */
    public function testMustReturnTrueForForwardSlashWinningPatterns($pattern)
    {
        /** @var PlayerAbstract $playerOne */
        $playerOne = $this->container->get('connect4.player.human');
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        // Return true for forward slash winning patterns
        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkForwardSlashPattern(Board::TOKEN_PLAYER_ONE));

        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));

    }

    /**
     * Test forward slash (\) winning patterns
     *
     * @param $pattern
     * @dataProvider backSlashWinningPatternProviders
     */
    public function testMustReturnTrueForBackSlashWinningPatterns($pattern)
    {
        /** @var PlayerAbstract $playerOne */
        $playerOne = $this->container->get('connect4.player.human');
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        // Return true for back slash winning patterns
        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkBackSlashPattern(Board::TOKEN_PLAYER_ONE));

        $this->movesStore->setCells($pattern);
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));
    }

    /**
     * @dataProvider noWinnerPatternProviders
     * @param $cells
     */
    public function testMustReturnFalseWhenTheresNoWinnerInThePattern($cells)
    {
        $playerOne = $this->container->get('connect4.player.human');
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        $playerTwo = $this->container->get('connect4.player.ai');
        $playerTwo->setToken(Board::TOKEN_PLAYER_TWO);

        // Return true for horizontal winning patterns
        $this->movesStore->setCells($cells);
        self::assertFalse($this->movesStore->checkWinningPatterns($playerOne));
        self::assertFalse($this->movesStore->checkWinningPatterns($playerTwo));
    }

    public function noWinnerPatternProviders()
    {
        $cells = [
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
        ];

        return [
            [$cells]
        ];
    }

    public function backSlashWinningPatternProviders()
    {
        $backSlash1 = [
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash2 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash3 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
        ];

        $backSlash4 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash5 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash6 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash7 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$backSlash1],
            [$backSlash2],
            [$backSlash3],
            [$backSlash4],
            [$backSlash5],
            [$backSlash6],
            [$backSlash7],
        ];

    }

    public function forwardSlashWinningPatternProviders()
    {
        $forwardSlash1 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash2 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash3 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash4 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash5 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash6 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash7 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$forwardSlash1],
            [$forwardSlash2],
            [$forwardSlash3],
            [$forwardSlash4],
            [$forwardSlash5],
            [$forwardSlash6],
            [$forwardSlash7],
        ];
    }

    public function verticalWinningPatternProviders()
    {
        $vertical1 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical2 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical3 = [
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical4 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical5 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE],
        ];

        $vertical6 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical7 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical8 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$vertical1],
            [$vertical2],
            [$vertical3],
            [$vertical4],
            [$vertical5],
            [$vertical6],
            [$vertical7],
            [$vertical8],
        ];
    }

    public function horizontalWinningPatternProviders()
    {
        $horizontalPattern1 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
        ];

        $horizontalPattern2 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $horizontalPattern3 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $horizontalPattern4 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $horizontalPattern5 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $horizontalPattern6 = [
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $horizontalPattern7 = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$horizontalPattern1],
            [$horizontalPattern2],
            [$horizontalPattern3],
            [$horizontalPattern4],
            [$horizontalPattern5],
            [$horizontalPattern6],
            [$horizontalPattern7],
        ];
    }

    public function cellProvider1()
    {
        $cells = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$cells]
        ];
    }
}