<?php

// Utility helper functions

/**
 * Dump a variable and die
 * @param $var
 */
function dd($var)
{
    var_dump($var);
    die();
}

/**
 * Show success message in green!
 *
 * @param $message
 */
function printSuccess($message)
{
    echo "\033[42mSuccess: $message \033[0m\n";
}

/**
 * Show error message in red!
 *
 * @param $error
 */
function printError($error)
{
    echo "\033[31mError: $error \033[0m\n";
}

/**
 * Show info message in yellow!
 *
 * @param $info
 * @param bool $withNewLine
 */
function printInfo($info, $withNewLine = true)
{
    $newLine = $withNewLine ? "\n" : "";
    echo "\033[33mInfo: $info \033[0m" . $newLine;
}

/**
 * Get a different exit instruction if it's in docker
 *
 * @return string
 */
function getExitInstruction()
{
    return getenv('IN_DOCKER') == 1
        ? "Press Ctrl+p+q anytime to exit the game.\n"
        : "Press Ctrl+C anytime to exit the game.\n";
}