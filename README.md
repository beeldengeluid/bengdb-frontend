BengDB frontend
===============
## Installing
To install the dependencies for this project you need [bower](http://bower.io) (for frontend assets) and [Composer](http://getcomposer.org) (for PHP libraries).

First copy `config-sample.php` in the `web` directory to `config.php` and change the values. You need a [Chantek](http://github.com/hay/chantek) server for the API calls.

Then, in the root of the project, install the dependencies

    bower install
    composer install

You should be able to run the application in `web/` now.

Note that you either need `mod_rewrite` (for Apache) or some other redirection magic to get pretty urls.

## Credits
This frontend is based on [The Sum of All Knowledge](https://github.com/hay/sum)

* Written by [Hay Kranen](http://github.com/hay)

With contributions by:
* [Tisza Gergö](https://github.com/tgr)