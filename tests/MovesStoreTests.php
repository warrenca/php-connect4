<?php

namespace Connect4\Tests;


use Connect4\Player\HumanPlayer;
use Connect4\Store\MovesStore;
use Connect4\View\Board;
use PHPUnit\Framework\TestCase;

class MovesStoreTests extends TestCase
{

    /** @var  MovesStore */
    private $movesStore;

    public function setup()
    {
        $this->movesStore = new MovesStore();
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
        self::assertEquals("Invalid column, please only choose from 1, 2, 3, 4, 5, 6, 7", $this->movesStore->getError());
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
    public function testMustReturnFalseIfColumnIsNotInRange()
    {
        self::assertFalse($this->movesStore->isColumnInRange(Board::COLUMNS));
        self::assertEquals("Invalid column, please only choose from 1, 2, 3, 4, 5, 6, 7", $this->movesStore->getError());
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
        self::assertEquals(4, $this->movesStore->getNextAvailableRowIndex(0));
    }

    /**
     * Must return true for winning patterns
     * @param $horizontal
     * @param $vertical
     * @param $forwardSlash
     * @param $backSlash
     * @dataProvider winningPatternProviders
     */
    public function testMustReturnTrueForWinningPatters($horizontal, $vertical, $forwardSlash, $backSlash)
    {
        $playerOne = new HumanPlayer();
        $playerOne->setToken(Board::TOKEN_PLAYER_ONE);

        // Return true for horizontal winning patterns
        $this->movesStore->setCells($horizontal);
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));
        self::assertTrue($this->movesStore->checkHorizontalPattern(Board::TOKEN_PLAYER_ONE));

        // Return true for vertical winning patterns
        $this->movesStore->setCells($vertical);
        self::assertTrue($this->movesStore->checkVerticalPattern(Board::TOKEN_PLAYER_ONE));
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));

        // Return true for forward slash winning patterns
        $this->movesStore->setCells($forwardSlash);
        self::assertTrue($this->movesStore->checkForwardSlashPattern(Board::TOKEN_PLAYER_ONE));
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));

        // Return true for back slash winning patterns
        $this->movesStore->setCells($backSlash);
        self::assertTrue($this->movesStore->checkBackSlashPattern(Board::TOKEN_PLAYER_ONE));
        self::assertTrue($this->movesStore->checkWinningPatterns($playerOne));
    }

    public function winningPatternProviders()
    {
        $horizontal = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $vertical = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_TWO, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_TWO, Board::TOKEN_PLAYER_ONE, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $forwardSlash = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $backSlash = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_PLAYER_ONE, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        return [
            [$horizontal, $vertical, $forwardSlash, $backSlash]
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