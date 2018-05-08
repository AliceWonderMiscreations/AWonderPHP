AWonderPHP Classes for WordPress
================================

A collection of classes that are used by my WordPress plugins.

These classes are intended to be loaded by the autoloader script you can find
at [https://gist.github.com/AliceWonderMiscreations/4ba7209256f0e2b38d59a8787d164f63]

Place that autoloader in your WordPress `mu-plugins` directory.

Then create the directory `wp-content/wp-psr4`

To install the classes:

    cd wp-psr4
    git clone https://github.com/AliceWonderMiscreations/AWonderPHP.git

LICENSE
-------

Most classes are MIT License. All classes will have the license defined in the
phpdoc header.

Composer
--------

The composer file only exists to install development tools. Do not run run
composer install unless you want them. They are useless inside a WordPress
install.