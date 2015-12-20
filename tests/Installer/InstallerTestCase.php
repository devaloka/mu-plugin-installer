<?php
namespace Tests\Devaloka\Composer\Installer;

use Composer\Composer;
use Composer\Config;
use Composer\Downloader\DownloadManager;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Mockery;
use PHPUnit_Framework_TestCase;

/**
 * Class InstallerTestCase
 *
 * @package Tests\Devaloka\Composer\Installer
 * @author Whizark
 */
abstract class InstallerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Mockery\Mock|DownloadManager
     */
    protected $downloadManager;

    /**
     * @var Mockery\Mock|InstalledRepositoryInterface
     */
    protected $repository;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var string
     */
    protected $vendorDir;

    /**
     * @var string
     */
    protected $binDir;

    /**
     * Sets up the test case.
     */
    protected function setUp()
    {
        $this->config     = new Config();
        $this->composer   = new Composer();
        $this->filesystem = new Filesystem();

        $this->downloadManager = Mockery::mock('Composer\Downloader\DownloadManager');
        $this->repository      = Mockery::mock('Composer\Repository\InstalledRepositoryInterface');
        $this->io              = Mockery::mock('Composer\IO\IOInterface');

        $this->vendorDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'installer-tests-vendor';
        $this->binDir    = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'installer-tests-bin';

        $this->config->merge(
            array(
                'config' => array(
                    'vendor-dir' => $this->vendorDir,
                    'bin-dir'    => $this->binDir,
                ),
            )
        );
        $this->composer->setConfig($this->config);
        $this->composer->setDownloadManager($this->downloadManager);

        $this->filesystem->emptyDirectory($this->vendorDir);
        $this->filesystem->emptyDirectory($this->binDir);
    }

    /**
     * Tears down the test case.
     */
    protected function tearDown()
    {
        $this->filesystem->removeDirectory($this->vendorDir);
        $this->filesystem->removeDirectory($this->binDir);
    }
}
