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

``` php
# Not shown: include composer's autoload.php
use bouiboui\Tissue\Tissue;

try {

    throw new ErrorException('This is your issue title and message.');

} catch (\ErrorException $e) {

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

All parameters are optional. For security purposes, think twice before setting the `trace` parameter if your Github repository is public, unless you want strangers on the Internet to know the full path to the files on your server.

## Credits

- bouiboui — [Github](https://github.com/bouiboui) [Twitter](https://twitter.com/j_____________n) [Website](http://cod3.net)

## License

Unlicense. Public domain, basically. Please treat it kindly. See [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/bouiboui/tissue.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Unlicense-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/bouiboui/tissue
