# Interactive Connect4 game #

Connect Four (also known as Captain's Mistress, Four Up, Plot Four, Find Four, Four in a Row, Four in a Line and Gravitrips) is a two-player connection game in which the players first choose a color and then take turns dropping colored discs from the top into a seven-column, six-row vertically suspended grid.

### Assumptions ###

1. An interactive text console game ✅
2. Written in Ruby or PHP ✅
3. Game board should be 7 columns by 6 rows ✅
4. Show a graphical ascii representation of state of game between each move ✅
5. Has a human player and a computer player ✅
6. When it is player’s move: prompt for which column to drop token into ✅
7. Indicate which moves are valid before prompting ✅
8. Show an error message when an invalid column is given, and prompt again ✅
9. Computer move: computer player can be dumb, but it must make a valid move ✅
10. Detect and announce winner immediately after winning move is made ✅
11. The software design should allow for easily replacing the computer player /
adding a new computer player - i.e. the design makes it easy to add a smart
computer player in the future, or even choose between different computer player
strategies, or have two human players, or two computer players ✅
12. There may be opportunity to use patterns like MVC, dependency injection. Expect to see
unit testing. Prefer minimal dependencies. ✅

### Concepts ###

- This project is well tested using `PHPUnit` testing framework.
- This project uses dependency injection using `PHP Interface` and dependency injection container using the package `php-di`.
- This project also demonstrates the use of `Abstract Class` that is inherited by the `Players Classes`.
- As a data storage, it uses the `(model) MovesStore class`  to handle the saving and validating player movements.
- Moreover, the `(view) Board class`  act as the manager to display the game movements visually.
- Finally, the `(controller) Game class`  directs the game.

#### File and Directory Structure ####

* `php-di` - The definition and configuration of dependency injection + DI container
* `src/Connect4` - A directory for all the classes used to run the game
* `src/Connect4/Game.php` - A file that links tha Players, its movements (`MovesStore class`) and how to display it (`Board class`); 
* `src/Connect4/Player` - A directory for Player related Classes
* `src/Connect4/Store` - A directory related to storing and retrieving data from the game
* `src/Connect4/View` - A directory related to displaying the movements to the user
* `tests` - A directory for project testing
* `utils` - A directory for helper functions
* `bootstrap.php` - A file that loads all the required classes for the game to run
* `main.php` - The main file that will run the game

### What is this repository for? ###

* This project is a an interactive Connect4 game. The possible match ups are: Human vs Human, Human vs AI and AI vs AI.
* v1.0.0
* [Source Code](https://bitbucket.org/warrenca/connect4/)

### How do I get set up? ###

#### Configuration

There's no configuration required for the game to play.
The game lets you choose from different game modes: Human vs Human, Human vs AI and AI vs AI.

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
Connect4 Game!
Please choose a game mode.
1) Human vs Human
2) Human vs AI
3) AI vs AI
 
Enter 1, 2 or 3: 2

Hey! Welcome to Connect4 game.
It is a turn-based game between two players.
Each player simply needs to enter a column number 
where they want to drop their token.
To win, they must Connect4 tokens of their own either
horizontally, vertically or diagonally.
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
> phpunit --testdox
PHPUnit 7.0.2 by Sebastian Bergmann and contributors.

Connect4\Tests\BoardTests
 ✔ Constants should be as defined
 ✔ On init rows and columns length should be as defined
 ✔ On init cells should be empty
 ✔ Canvas drawing should be as expected data set #0

Connect4\Tests\PlayerTests
 ✔ Must set and get name data set #0
 ✔ Must set and get token
 ✔ Must be a human or ai
 ✔ Must set and get moves store class
 ✔ Must have selected a column within range

Connect4\Tests\MovesStoreTests
 ✔ Constants should be as defined
 ✔ Must set and get cells data set #0
 ✔ Must set and get error
 ✔ Must return true if column is full data set #0
 ✔ Must return valid columns data set #0
 ✔ Must return false if column is not full
 ✔ Must return true if column is in range
 ✔ Must return false when dropping to incorrect column index data set #0
 ✔ Must return true when dropping to correct column index data set #0
 ✔ Must return false if column is not in range data set #0
 ✔ Must get the correct row index data set #0
 ✔ Must return true for winning patters data set #0
 ✔ Must return false when theres no winner in the pattern data set #0

Connect4\Tests\GameTests
 ✔ Must get moves store class
 ✔ Must get board class
 ✔ Must get player one as dumb ai player class
 ✔ Must not get player one
 ✔ Must get players as player abstract
 ✔ Must set and get current player
 ✔ Must set and get a winner
 ✔ Must return maximum turns
 ✔ Must match class setup
 ✔ Must return a winner
 ✔ Must return no winner

Time: 2.04 seconds, Memory: 4.00MB

OK (33 tests, 111 assertions)
```

### Contribution guidelines ###

#### Writing tests

This project is using `phpunit` testing framework.
The PHPUnit test configuration file is `phpunit.xml`.
Add new tests in `./tests` directory.

### Who do I talk to? ###

* @warrenca github/bitbucket
