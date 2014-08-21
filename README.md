dhawton\\OAuth
=====================
[![License](http://www.alphawhiskey.org/projects/icons/mit.svg)](http://www.github.com/dhawton/OAuth)

[dhawton\\OAuth](http://github.com/dhawton/oauth) is a plugin system based on code originally written by
Andy Smith to create a standard library for the OAuth 1.0 standards that follows the PSR standards of coding.

Installation
------------

For composer users, install dhawton\OAuth by inserting the following into `composer.json`:

    {
        "require": {
            "dhawton/OAuth": "dev-master"
        }
    }

For users not using composer, require "lib/dhawton/OAuth/Autoloader.php" and execute:

dhawton\OAuth\Autoloader::register();