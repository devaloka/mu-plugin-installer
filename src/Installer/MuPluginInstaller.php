<?php
/**
 * Mu Plugin Installer
 *
 * @author Whizark <devaloka@whizark.com>
 * @see http://whizark.com
 * @copyright Copyright (C) 2015 Whizark.
 * @license MIT
 */

namespace Devaloka\Composer\Installer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use React\Promise\PromiseInterface;

/**
 * Class MuPluginInstaller
 *
 * @package Devaloka\Composer
 */
class MuPluginInstaller extends LibraryInstaller
{
    /**
     * @var string The package type name.
     */
    const TYPE = 'devaloka-muplugin';

    /**
     * @var string[] The configuration values from a package's `composer.json`.
     */
    protected $installerConfig;

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return ($packageType === static::TYPE);
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $loader = $this->getLoaderFileInstallPath($package);

        return (parent::isInstalled($repo, $package) || is_readable($loader));
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $config      = $this->getInstallerConfig($package);
        $installPath = 'wp-content/mu-plugins/{$name}/';

        if (!$this->composer->getPackage()) {
            return $this->parseTemplate($installPath, $config);
        }

        $extra      = $this->composer->getPackage()->getExtra();
        $prettyName = !empty($config['vendor']) ? ($config['vendor'] . '/') : '';
        $prettyName = $prettyName . $config['name'];

        if (!empty($extra['installer-paths'])) {
            $customPath = $this->resolveInstallPath($extra['installer-paths'], $prettyName);

            if ($customPath !== false) {
                $installPath = $customPath;
            }
        }

        return $this->parseTemplate($installPath, $config);
    }

    /**
     * Gets the install path for the loader script of an MU plugin.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     *
     * @return string|bool The install path, or `false` if the path cannot be resolved.
     */
    public function getLoaderInstallPath(PackageInterface $package)
    {
        $config      = $this->getInstallerConfig($package);
        $installPath = 'wp-content/mu-plugins/';

        if (!$this->composer->getPackage()) {
            return $this->parseTemplate($installPath, $config);
        }

        $extra      = $this->composer->getPackage()->getExtra();
        $prettyName = !empty($config['vendor']) ? ($config['vendor'] . '/') : '';
        $prettyName = $prettyName . $config['name'];

        if (!empty($extra['installer-loader-paths'])) {
            $customPath = $this->resolveInstallPath($extra['installer-loader-paths'], $prettyName);

            if ($customPath !== false) {
                $installPath = $customPath;
            }
        }

        return $this->parseTemplate($installPath, $config);
    }

    /**
     * Gets the file path where a loader script is located.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     *
     * @return string The file path, or `false` if the path cannot be resolved.
     */
    public function getLoaderFilePackagePath(PackageInterface $package)
    {
        $installPath = $this->getInstallPath($package);

        if ($installPath === false) {
            return false;
        }

        $config      = $this->getInstallerConfig($package);
        $packagePath = $installPath . $config['loader'];

        return $packagePath;
    }

    /**
     * Gets the file path where a loader script is installed.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     *
     * @return string The file path, or `false` if the path cannot be resolved.
     */
    public function getLoaderFileInstallPath(PackageInterface $package)
    {
        $installPath = $this->getLoaderInstallPath($package);

        if ($installPath === false) {
            return false;
        }

        $config      = $this->getInstallerConfig($package);
        $installPath = $installPath . basename($config['loader']);

        return $installPath;
    }

    /**
     * Retrieves configuration values from a packages's `composer.json`.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     *
     * @return string[] The configuration values.
     */
    protected function getInstallerConfig(PackageInterface $package)
    {
        if ($this->installerConfig !== null) {
            return $this->installerConfig;
        }

        $type       = $package->getType();
        $prettyName = $package->getPrettyName();

        if (strpos($prettyName, '/') !== false) {
            list($vendor, $name) = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name   = $prettyName;
        }

        $extra = $package->getExtra();

        if (!empty($extra['installer-name'])) {
            $name = $extra['installer-name'];
        }

        $loader = 'mu-plugins/' . $name . '.php';

        if (!empty($extra['installer-loader'])) {
            $loader = $extra['installer-loader'];
        }

        return compact('name', 'vendor', 'type', 'loader');
    }

    /**
     * Parses a template string.
     *
     * @param string $template A template string.
     * @param mixed[] $vars Template variables.
     *
     * @return string The parsed template string.
     *
     * @see https://github.com/composer/installers This code is based on the Composer Installer.
     */
    protected function parseTemplate($template, array $vars = array())
    {
        if (strpos($template, '{') === false) {
            return $template;
        }

        if (preg_match_all('@\{\$([A-Za-z0-9_]+)\}@i', $template, $matches)) {
            foreach ($matches[1] as $varName) {
                $template = str_replace('{$' . $varName . '}', $vars[$varName], $template);
            }
        }

        return $template;
    }

    /**
     * Searches the install path based on a package name and a paths array.
     *
     * @param array[] $paths A paths array.
     * @param string $name A package name.
     *
     * @return string|bool The install path, or `false` if the path cannot be resolved.
     *
     * @see https://github.com/composer/installers This code is based on the Composer Installer.
     */
    protected function resolveInstallPath(array $paths, $name)
    {
        foreach ($paths as $path => $names) {
            if (in_array($name, $names, true) || in_array('type:' . static::TYPE, $names, true)) {
                return $path;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function installCode(PackageInterface $package)
    {
        $muPluginInstaller = $this;

        $installLoader = function () use ($muPluginInstaller, $package) {
            $muPluginInstaller->installLoader($package);
        };

        $promise = parent::installCode($package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($installLoader);
        }

        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        $installLoader();
    }

    /**
     * Installs the loader script of an MU plugin.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     */
    protected function installLoader(PackageInterface $package)
    {
        $source = $this->getLoaderFilePackagePath($package);
        $target = $this->getLoaderFileInstallPath($package);

        copy($source, $target);
    }

    /**
     * {@inheritDoc}
     */
    protected function removeCode(PackageInterface $package)
    {
        $muPluginInstaller = $this;

        $removeLoader = function () use ($muPluginInstaller, $package) {
            $muPluginInstaller->removeLoader($package);
        };

        $promise = parent::removeCode($package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($removeLoader);
        }

        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        $removeLoader();
    }

    /**
     * Removes the loader script of an MU plugin.
     *
     * @param PackageInterface $package An instance of PackageInterface.
     */
    protected function removeLoader(PackageInterface $package)
    {
        $target = $this->getLoaderFileInstallPath($package);

        if (!$this->filesystem->remove($target)) {
            throw new \RuntimeException('Could not completely delete ' . $target . ', aborting.');
        }
    }
}
