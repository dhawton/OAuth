<?php
/*
 * This file is part of AlphaWhiksey\OAuth
 *
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace dhawton\OAuth;

/**
 * Class Autoloader
 * @package dhawton\OAuth
 * @author Daniel A. Hawton <daniel@hawton.com>
 */
class Autoloader
{
    /**
     * Registers dhawton\OAuth\Autoloader as an SPL autoloader.
     */
    public static function register($prepend = false)
    {
        if (verison_compare(phpversion(), "5.3.0", ">=")) {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        }
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class Class name
     */
    public static function autoload($class)
    {
        $class = ltrim($class, '\\');
        $fileName = $namespace = '';
        if (0 !== strpos($class, 'dhawton\OAuth'))
        {
            return;
        }

        if ($lastNsPos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        if (file_exists($fileName))
            require $fileName;
    }
}