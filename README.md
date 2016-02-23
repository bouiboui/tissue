# tissue

[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]](LICENSE)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fdc3c186-f787-4427-9c81-a3f82f3db720/mini.png)](https://insight.sensiolabs.com/projects/fdc3c186-f787-4427-9c81-a3f82f3db720)

Create Github issues from your ``catch {}`` blocks. I was heavily inspired by [ohCrash](https://ohcrash.com/).

When you call ``Tissue::create``, a Github issue is created in the repo of your choice and a "bug" label is automatically applied. Duplicates are detected, to a certain extent.

The name comes from "Throw ISSUE" — genius, I know.

## Install

``` bash
$ composer require bouiboui/tissue
```

Create a local ``config/config.yaml`` file from the template in [``config/config.yaml.dist``](https://github.com/bouiboui/tissue/blob/master/config/config.yaml.dist)

## Usage

**The easy way: `bindUncaughtExceptionHandler`**
``` php
// Not shown: include composer's autoload.php
use bouiboui\Tissue\Tissue;

// All uncaught exceptions will trigger the creation of a Github issue
Tissue::bindUncaughtExceptionHandler();
```
**The catch-block-specific way: `createFromException`**
``` php
// Not shown: include composer's autoload.php
use bouiboui\Tissue\Tissue;

try {

    throw new ErrorException('This is your issue title and message.');

} catch (\ErrorException $e) {

    // Only exceptions caught by this block will create Github issues
    $result = Tissue::createFromException($e);

}
```

**The "customized output" way**
``` php
// Not shown: include composer's autoload.php
use bouiboui\Tissue\Tissue;

try {

    throw new ErrorException('This is your issue title and message.');

} catch (\ErrorException $e) {

    // Set any parameter to null if you don't want to display it in the issue
    $result = Tissue::create(
        $e->getMessage(),
        $e->getCode(),
        $e->getSeverity(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    
    var_dump($result);

}
```
Creates the following issue:

![Something like this](http://i.imgur.com/N5r8Ljh.png)
![Something like this](http://i.imgur.com/a96l7hR.png)

And outputs the following:

``` php
array(3) {
  ["duplicate"]=>
  bool(false)
  ["number"]=>
  int(35)
  ["url"]=>
  string(50) "https://api.github.com/repos/author/name/issues/35"
}
```

For security purposes, if your Github repository is public you should at the *very* least disable the `trace` parameter, unless you want strangers on the Internet to know the full path to the files on your server. [You may also want to read this](https://www.owasp.org/index.php/Improper_Error_Handling#Description).

## Credits

- bouiboui — [Github](https://github.com/bouiboui) [Twitter](https://twitter.com/j_____________n) [Website](http://cod3.net)
- [All contributors](https://github.com/bouiboui/tissue/graphs/contributors)

## License

Unlicense. Public domain, basically. Please treat it kindly. See [License File](LICENSE) for more information. 

This project uses the following open source projects 
- [knplabs/github-api](https://github.com/KnpLabs/php-github-api) by [KnpLabs](https://github.com/KnpLabs) — [License](https://github.com/KnpLabs/php-github-api/blob/master/LICENSE).
- [symfony/yaml](https://github.com/symfony/yaml) by [Fabien Potencier](https://github.com/fabpot) — [License](https://github.com/symfony/yaml/blob/master/LICENSE).
- [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit) by [Sebastian Bergmann](https://github.com/sebastianbergmann) — [License](https://github.com/sebastianbergmann/phpunit/blob/master/LICENSE).

[ico-version]: https://img.shields.io/packagist/v/bouiboui/tissue.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Unlicense-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/bouiboui/tissue
