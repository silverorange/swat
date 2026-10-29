# Swat

Swat is a web application component toolkit.

Swat is an open-source
[LGPL 2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt)
web-application toolkit built using [PHP](https://www.php.net/). It is primarily
developed and maintained by [silverorange](https://www.silverorange.com), but
participation and contributions are welcome.

Swat is _not_ a full-stack web framework. Swat is an interface toolkit
analogous to [Gtk](https://gtk.org/ Gtk), but for web applications instead of
native applications. Read about
[why Swat was created](https://github.com/silverorange/swat/wiki/History).

## Documentation

- [Simple Example](https://github.com/silverorange/swat/wiki/Simple-Example)

## Features

- object-oriented PHP 5 code
- simplifies the process of building and maintaining functional user
  interfaces for web applications
- generates standards-compliant XHTML output with flexible CSS hooks
- relies on CSS for layout and styling
- provides comprehensive and extensible error checking for forms

## Installation

```sh
composer require silverorange/swat
```

## Demo Application

Assuming you have PHP installed and in your `$PATH`, from the root of this repository, run:

```sh
composer install
php -S localhost:8888 -t demo/www
```

Then open http://localhost:8888/ in your browser.
