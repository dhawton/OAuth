AlphaWhiskey\\OAuth
=====================
[![License](http://www.alphawhiskey.org/projects/icons/mit.svg)](http://www.github.com/alphawhiskey/OAuth)

[AlphaWhiskey\\OAuth](http://github.com/alphawhiskey/oauth) is a plugin system based on code originally written by
Andy Smith to create a standard library for the OAuth 1.0 standards that follows the PSR standards of coding.

Installation
------------

For composer users, install AlphaWhiskey\OAuth by inserting the following into `composer.json`:

    {
        "require": {
            "AlphaWhiskey/OAuth": "dev-master"
        }
    }

For users not using composer, require "lib/AlphaWhiskey/OAuth/Autoloader.php" and execute:

AlphaWhiskey\OAuth\Autoloader::register();