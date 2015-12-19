# A Composer Installer for WordPress MU Plugins

[![Build Status](https://travis-ci.org/devaloka/mu-plugin-installer.svg?branch=master)](https://travis-ci.org/devaloka/mu-plugin-installer) [![Packagist](https://img.shields.io/packagist/v/devaloka/mu-plugin-installer.svg)](https://packagist.org/packages/devaloka/mu-plugin-installer)

This is a Composer Installer for WordPress MU Plugins.

The Installer is basically based on/compatible with [A Multi-Framework Composer Library Installer](https://github.com/composer/installers)
but it also supports the loader script installation of your MU plugin.

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

    *   your-plugin.php (loader script)

    *   *your-plugin*/ (same as your package name by default)

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
        "devaloka/mu-plugin-installer": "~0.1.0"
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

`{$vendor}`, `{$name}` and `{$type}` variables are available.

In addition, `{$loader}` variable is available, which is the relative path
to the loader file.

You can check out a real world [package.json](https://github.com/devaloka/devaloka/blob/master/package.json) example.

## Example `composer.json` File (for root package)

The root package means your project's `package.json`.

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

`{$vendor}`, `{$name}`, `{$type}` and `{$loader}` are available.
