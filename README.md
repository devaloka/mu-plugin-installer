# A Composer Installer for WordPress MU Plugins

[![Latest Stable Version][stable-image]][stable-url]
[![Latest Unstable Version][unstable-image]][unstable-url]
[![License][license-image]][license-url]
[![Build Status][travis-image]][travis-url]
[![Coverage Status][coveralls-image]][coveralls-url]
 
This is a Composer Installer for WordPress MU Plugins.

The Installer is basically based on/compatible with [A Multi-Framework Composer Library Installer](https://github.com/composer/installers)
but it also **supports the loader script installation** of your MU plugin.

The loader script is installed into `mu-plugins` directory by default so that
you can provide your MU plugin including sub directory as a Composer package.

## Example

A package:

*   *your-package-root*

    *   mu-plugins/ (loader directory: `mu-plugins` by default)

        *   your-plugin.php (loader script: same as your package name by
            default)

    *   foo/

        *   bar.php 

    *   baz.php

will be installed as:

*   wp-content/mu-plugins/

    *   your-plugin.php (**loader script**)

    *   *your-plugin*/ (same as your package name by default)

        *   mu-plugins/ (original loader directory remains)

            *   your-plugin.php (original loader script remains)

        *   foo/

            *   bar.php

        *   baz.php

## Example `composer.json` File (for MU plugin package)

`composer.json` becomes almost the same as [A Multi-Framework Composer Library Installer](https://github.com/composer/installers)'s.

### Package Type and Dependency (`type` and `require`)

```json
{
    "name": "you/your-plugin-name",
    "type": "devaloka-muplugin",
    "require": {
        "devaloka/mu-plugin-installer": "~0.2.0"
    }
}
```

### Custom Loader File (`installer-loader`)

`installer-loader` key is available for your custom loader file, which is the
relative path from your package root.

```json
{
    "extra": {
        "installer-loader": "loader/your-loader.php"
    }
}
```

You can check out a real world [composer.json](https://github.com/devaloka/devaloka/blob/master/composer.json) example.

## Example `composer.json` File (for root package)

The root package means your project's `composer.json`.

### Custom Loader Path (`installer-loader-paths`)

`installer-loader-paths` key is available for your custom install path for
loader(s).

This is almost the same as as [A Multi-Framework Composer Library Installer](https://github.com/composer/installers)'s `installer-paths`
but it is for the loader file.

```json
{
    "extra": {
        "installer-loader-paths": {
            "your-custom-path/{$name}/": ["vendor/package"]
        }
    }
}
```

With a `type:` prefix:

```json
{
    "extra": {
        "installer-loader-paths": {
            "your-custom-path/{$name}/": ["type:devaloka-muplugin"]
        }
    }
}
```

`{$vendor}`, `{$name}` and `{$type}` variables are available.

In addition, `{$loader}` variable is available, which is the relative path
to the loader file.

[stable-image]: https://poser.pugx.org/devaloka/mu-plugin-installer/v/stable
[stable-url]: https://packagist.org/packages/devaloka/mu-plugin-installer

[unstable-image]: https://poser.pugx.org/devaloka/mu-plugin-installer/v/unstable
[unstable-url]: https://packagist.org/packages/devaloka/mu-plugin-installer

[license-image]: https://poser.pugx.org/devaloka/mu-plugin-installer/license
[license-url]: https://packagist.org/packages/devaloka/mu-plugin-installer

[travis-image]: https://travis-ci.org/devaloka/mu-plugin-installer.svg?branch=master
[travis-url]: https://travis-ci.org/devaloka/mu-plugin-installer

[coveralls-image]: https://coveralls.io/repos/devaloka/mu-plugin-installer/badge.svg?branch=master&service=github
[coveralls-url]: https://coveralls.io/github/devaloka/mu-plugin-installer?branch=master
