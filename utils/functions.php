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
    echo "\033[42m$message \033[0m\n";
}

/**
 * Show error message in red!
 *
 * @param $error
 */
function printError($error)
{
    echo "\033[31m$error \033[0m\n";
}

/**
 * Show info message in yellow!
 *
 * @param $info
 */
function printInfo($info)
{
    echo "\033[33m$info \033[0m\n";
}