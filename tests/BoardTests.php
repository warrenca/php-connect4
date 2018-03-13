<?php

namespace Connect4\Tests;


use Connect4\View\Board;
use PHPUnit\Framework\TestCase;

final class BoardTests extends TestCase
{
    /**
     * ALl constants must match based on what was defined in the Board class
     */
    public function testConstantsShouldBeAsDefined()
    {
        self::assertEquals(7, Board::COLUMNS);
        self::assertEquals(6, Board::ROWS);
        self::assertEquals('[ ]', Board::TOKEN_EMPTY_CELL);
        self::assertEquals('[X]', Board::TOKEN_PLAYER_ONE);
        self::assertEquals('[O]', Board::TOKEN_PLAYER_TWO);
    }

    /**
     * On board initialisation, the rows and columns must match
     * the defined amount in the Board class
     */
    public function testOnInitRowsAndColumnsShouldLengthShouldBeAsDefined()
    {
        $board = new Board();
        $board->init();

        $cells = $board->getCells();

        self::assertEquals(count($cells), Board::ROWS);

        for ($rowIndex = 0; $rowIndex < Board::ROWS; $rowIndex++)
        {
            self::assertEquals(count($cells[$rowIndex]), Board::COLUMNS);
        }
    }

    /**
     * On initialisation, the Board cells should be empty
     */
    public function testOnInitCellsShouldBeEmpty()
    {
        $board = new Board();
        $board->init();

        $cells = $board->getCells();

        for ($rowIndex = 0; $rowIndex < Board::ROWS; $rowIndex++)
        {
            for ($columnIndex = 0; $columnIndex < Board::COLUMNS; $columnIndex++)
            {
                self::assertEquals(Board::TOKEN_EMPTY_CELL, $cells[$rowIndex][$columnIndex]);
            }
        }

    }

    /**
     * The display must match the expected display based on the cells defined
     *
     * @dataProvider emptyCellsProvider
     * @param $cells
     * @param $expectedCanvas
     */
    public function testCanvasDrawing($cells, $expectedCanvas)
    {
        $board = new Board();
        $board->setCells($cells);
        $canvas = $board->draw(false);

        self::assertEquals($expectedCanvas, $canvas);
    }

    public function emptyCellsProvider()
    {
        $cells = [
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
            [Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL, Board::TOKEN_EMPTY_CELL],
        ];

        $expectedCanvas = "    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
C->  1  2  3  4  5  6  7

";

        return [
            [$cells, $expectedCanvas]
        ];
    }
}