# Connect4 game simulation #

Connect Four (also known as Captain's Mistress, Four Up, Plot Four, Find Four, Four in a Row, Four in a Line and Gravitrips) is a two-player connection game in which the players first choose a color and then take turns dropping colored discs from the top into a seven-column, six-row vertically suspended grid.

### Assumptions ###

### Concepts ###

- This project is well tested using `PHPUnit` testing framework.
- This project uses dependency injection using `PHP Interface` and dependency injection container using the package `php-di`.
- It also demonstrates the use of `Abstract Class` that is inherited by the `Players Classes`.
- As a data storage, it uses the `(model) MovesStore class`  to handle the saving and validating player movements.
- Moreover, the `(view) Board class`  act as the manager to display the game movements visually.
- Finally, the `(controller) Game class`  directs the game.


### What is this repository for? ###

* This project is a simulation of Connect4 game. The possible match ups are: Human vs Human, Human vs AI, AI vs AI.
* v1.0
* [Source Code](https://bitbucket.org/warrenca/connect4/)

### How do I get set up? ###

#### Configuration

Currently the config is set between Human and a dumb AI.
To change the player settings, open up the file `./php-di/config.php` 
and change the parameters of the `Game class`. Possible configs are below.

```
# Human vs AI

return new \Connect4\Game(  $c->get('connect4.view.board'),
                            $c->get('connect4.player.human'),
                            $c->get('connect4.player.ai'),
                            $c->get('connect4.store.movesStore')
                          );

# Human vs Human

return new \Connect4\Game(  $c->get('connect4.view.board'),
                            $c->get('connect4.player.human'),
                            $c->get('connect4.player.human2'),
                            $c->get('connect4.store.movesStore')
                          );

# AI vs AI

return new \Connect4\Game(  $c->get('connect4.view.board'),
                            $c->get('connect4.player.ai'),
                            $c->get('connect4.player.ai2'),
                            $c->get('connect4.store.movesStore')
                          );
```

#### Dependencies

    * PHP v7.1
    * composer
        Composer Packages
        * php/di ^6.0
        * phpunit/phpunit ^7.0

#### Installation

Run `composer install`

#### Playing the game!!

Run `php main.php`

```
Hey! Welcome to Connect4 game simulation.
It is a turn based game between two players.
Each player simply needs to enter a column number 
where they want to drop their token.
To win, they must Connect4 tokens of their own either
horizontally, vertically and diagonally.
No one wins when neither player Connect's 4 token.
--------------------------------------
The players are...
Player One: Name Human 👤, Token [X]
Player Two: Name Robot 🤖, Token [O]
[ ] indicates an empty cell and a valid drop point.
Have fun!

 
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
    [ ][ ][ ][ ][ ][ ][ ]
C->  1  2  3  4  5  6  7

Human 👤[X]) Enter a column number: 
```

#### How to run tests

Run `composer test`

```
> phpunit
PHPUnit 7.0.2 by Sebastian Bergmann and contributors.

...............................                                   31 / 31 (100%)

Time: 2.04 seconds, Memory: 4.00MB

OK (31 tests, 108 assertions)

```

### Contribution guidelines ###

#### Writing tests

All test are found in `./tests` directory. This project is using `phpunit` testing framework.
The test configuration can be found in `phpunit.xml`.

### Who do I talk to? ###

* @warrenca github/bitbucket
