<?php
/**
 * Mu Plugin Installer Plugin
 *
 * @author Whizark <devaloka@whizark.com>
 * @see http://whizark.com
 * @copyright Copyright (C) 2015 Whizark.
 * @license MIT
 */

namespace Devaloka\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Class MuPluginInstallerPlugin
 *
 * @package Devaloka\Composer
 */
class MuPluginInstallerPlugin implements PluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new MuPluginInstaller($io, $composer);

        $composer->getInstallationManager()->addInstaller($installer);
    }
}
