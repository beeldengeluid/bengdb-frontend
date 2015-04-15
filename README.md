BengDB frontend
===============
## Installing

### System requirements
* PHP > 5.5
* MySQL or MariaDB. It *should* work with other PDO-compatible databases, but i haven't tried that.
* You'll also need a [Chantek](http://github.com/hay/chantek) server, which requires:
* Python 2.7
* A Redis server for caching

To install the dependencies for this project you need [bower](http://bower.io) (for frontend assets) and [Composer](http://getcomposer.org) (for PHP libraries).

First copy `config-sample.php` in the `web` directory to `config.php` and change the values. You need a [Chantek](http://github.com/hay/chantek) server for the API calls.

Then, in the root of the project, install the dependencies

    bower install
    composer install

You should be able to run the application in `web/` now.

Note that you either need `mod_rewrite` (for Apache) or some other redirection magic to get pretty urls. If you're using Nginx (recommended), look in etc/nginx-example.conf for an example configuration.

## Credits
This frontend is based on [The Sum of All Knowledge](https://github.com/hay/sum)

* Written by [Hay Kranen](http://github.com/hay)

With contributions by:
* [Tisza Gergö](https://github.com/tgr)