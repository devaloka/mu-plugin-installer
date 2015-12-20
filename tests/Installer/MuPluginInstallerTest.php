<?php
namespace Tests\Devaloka\Composer\Installer;

use Composer\Package\Package;
use Composer\Package\RootPackage;
use Devaloka\Composer\Installer\MuPluginInstaller;
use Mockery;

/**
 * Class MuPluginInstallerTest
 *
 * @package Tests\Devaloka\Composer\Installer
 * @author Whizark
 */
class MuPluginInstallerTest extends InstallerTestCase
{
    public function testPackageTypeShouldBeDevalokaMuplugin()
    {
        $this->assertSame('devaloka-muplugin', MuPluginInstaller::TYPE);
    }

    /**
     * @return mixed[]
     */
    public function typeDataProvider()
    {
        return array(
            array('devaloka-muplugin', true),
            array('unknown-type', false),
        );
    }

    /**
     * @dataProvider typeDataProvider
     *
     * @param string $type
     * @param bool $supports
     */
    public function testInstallerShouldSupportOnlyDevalokaMuplugin($type, $supports)
    {
        $installer = $this->createInstaller();

        $this->assertSame($supports, $installer->supports($type));
    }

    /**
     * @return mixed[]
     */
    public function defaultInstallPathDataProvider()
    {
        return array(
            array('package', 'wp-content/mu-plugins/package/'),
            array('tests/package', 'wp-content/mu-plugins/package/'),
        );
    }

    /**
     * @dataProvider defaultInstallPathDataProvider
     *
     * @param string $name
     * @param string $expectedPath
     */
    public function testDefaultInstallPath($name, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($name);

        $installPath = $installer->getInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function LoaderDefaultInstallPathDataProvider()
    {
        return array(
            array('package', 'wp-content/mu-plugins/'),
            array('tests/package', 'wp-content/mu-plugins/'),
        );
    }

    /**
     * @dataProvider loaderDefaultInstallPathDataProvider
     *
     * @param string $name
     * @param string $expectedPath
     */
    public function testLoaderDefaultInstallPath($name, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($name);

        $installPath = $installer->getLoaderInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function loaderFileDefaultPackagePathDataProvider()
    {
        return array(
            array('package', 'wp-content/mu-plugins/package/mu-plugins/package.php'),
            array('tests/package', 'wp-content/mu-plugins/package/mu-plugins/package.php'),
        );
    }

    /**
     * @dataProvider loaderFileDefaultPackagePathDataProvider
     *
     * @param string $name
     * @param string $expectedPath
     */
    public function testLoaderFileDefaultPackagePath($name, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($name);

        $packagePath = $installer->getLoaderFilePackagePath($package);

        $this->assertSame($expectedPath, $packagePath);
    }

    /**
     * @return mixed[]
     */
    public function loaderFileDefaultInstallPathDataProvider()
    {
        return array(
            array('package', 'wp-content/mu-plugins/package.php'),
            array('tests/package', 'wp-content/mu-plugins/package.php'),
        );
    }

    /**
     * @dataProvider loaderFileDefaultInstallPathDataProvider
     *
     * @param string $name
     * @param string $expectedPath
     */
    public function testDefaultLoaderFileInstallPath($name, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($name);

        $packagePath = $installer->getLoaderFileInstallPath($package);

        $this->assertSame($expectedPath, $packagePath);
    }

    /**
     * @return mixed[]
     */
    public function customNameInstallPathDataProvider()
    {
        return array(
            array('package', 'custom-name', 'wp-content/mu-plugins/custom-name/'),
            array('tests/package', 'custom-name', 'wp-content/mu-plugins/custom-name/'),
        );
    }

    /**
     * @dataProvider customNameInstallPathDataProvider
     *
     * @param string $package
     * @param string $name
     * @param string $expectedPath
     */
    public function testCustomNameInstallPath($package, $name, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($package);

        $package->setExtra(
            array(
                'installer-name' => $name,
            )
        );

        $installPath = $installer->getInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function customLoaderInstallPathDataProvider()
    {
        return array(
            array('package', 'path/to/loader.php', 'wp-content/mu-plugins/'),
            array('tests/package', 'path/to/loader.php', 'wp-content/mu-plugins/'),
        );
    }

    /**
     * @dataProvider customLoaderInstallPathDataProvider
     *
     * @param string $package
     * @param string $loader
     * @param string $expectedPath
     */
    public function testCustomLoaderInstallPath($package, $loader, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($package);

        $package->setExtra(
            array(
                'installer-loader' => $loader,
            )
        );

        $installPath = $installer->getLoaderInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function customLoaderFilePackagePathDataProvider()
    {
        return array(
            array('package', 'path/to/loader.php', 'wp-content/mu-plugins/package/path/to/loader.php'),
            array('tests/package', 'path/to/loader.php', 'wp-content/mu-plugins/package/path/to/loader.php'),
        );
    }

    /**
     * @dataProvider customLoaderFilePackagePathDataProvider
     *
     * @param $package
     * @param $loader
     * @param $expectedPath
     */
    public function testCustomLoaderFilePackagePath($package, $loader, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($package);

        $package->setExtra(
            array(
                'installer-loader' => $loader,
            )
        );

        $packagePath = $installer->getLoaderFilePackagePath($package);

        $this->assertSame($expectedPath, $packagePath);
    }

    /**
     * @return mixed[]
     */
    public function customLoaderFileInstallPathDataProvider()
    {
        return array(
            array('package', 'path/to/loader.php', 'wp-content/mu-plugins/loader.php'),
            array('tests/package', 'path/to/loader.php', 'wp-content/mu-plugins/loader.php'),
        );
    }

    /**
     * @dataProvider customLoaderFileInstallPathDataProvider
     *
     * @param string $package
     * @param string $loader
     * @param string $expectedPath
     */
    public function testCustomLoaderFileInstallPath($package, $loader, $expectedPath)
    {
        $installer = $this->createInstaller();
        $package   = $this->createMuPluginPackage($package);

        $package->setExtra(
            array(
                'installer-loader' => $loader,
            )
        );

        $installPath = $installer->getLoaderFileInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function customInstallPathDataProvider()
    {
        return array(
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'package',
                '-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'tests/package',
                'tests-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}',
                'unknown/package',
                'wp-content/mu-plugins/package/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'unknown/package',
                'wp-content/mu-plugins/package/',
            ),
        );
    }

    /**
     * @dataProvider customInstallPathDataProvider
     *
     * @param string $name
     * @param string $path
     * @param string $target
     * @param string $expectedPath
     */
    public function testCustomInstallPath($name, $path, $target, $expectedPath)
    {
        $installer       = $this->createInstaller();
        $package         = $this->createMuPluginPackage($name);
        $consumerPackage = new RootPackage('root/package', '1.0.0', '1.0.0');

        $this->composer->setPackage($consumerPackage);
        $consumerPackage->setExtra(
            array(
                'installer-paths' => array(
                    $path => array(
                        $target,
                    ),
                ),
            )
        );

        $installPath = $installer->getInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function LoaderCustomInstallPathDataProvider()
    {
        return array(
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'package',
                '-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'tests/package',
                'tests-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}',
                'unknown/package',
                'wp-content/mu-plugins/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'unknown/package',
                'wp-content/mu-plugins/',
            ),
        );
    }

    /**
     * @dataProvider LoaderCustomInstallPathDataProvider
     *
     * @param string $name
     * @param string $path
     * @param string $target
     * @param string $expectedPath
     */
    public function testLoaderCustomInstallPath($name, $path, $target, $expectedPath)
    {
        $installer       = $this->createInstaller();
        $package         = $this->createMuPluginPackage($name);
        $consumerPackage = new RootPackage('root/package', '1.0.0', '1.0.0');

        $this->composer->setPackage($consumerPackage);
        $consumerPackage->setExtra(
            array(
                'installer-loader-paths' => array(
                    $path => array(
                        $target,
                    ),
                ),
            )
        );

        $installPath = $installer->getLoaderInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function customInstallPathByTypeDataProvider()
    {
        return array(
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                MuPluginInstaller::TYPE,
                '-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                MuPluginInstaller::TYPE,
                'tests-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}',
                'unknown-type',
                'wp-content/mu-plugins/package/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'unknown-type',
                'wp-content/mu-plugins/package/',
            ),
        );
    }

    /**
     * @dataProvider customInstallPathByTypeDataProvider
     *
     * @param string $name
     * @param string $path
     * @param string $type
     * @param string $expectedPath
     */
    public function testCustomInstallPathByType($name, $path, $type, $expectedPath)
    {
        $installer       = $this->createInstaller();
        $package         = $this->createMuPluginPackage($name);
        $consumerPackage = new RootPackage('root/package', '1.0.0', '1.0.0');

        $this->composer->setPackage($consumerPackage);
        $consumerPackage->setExtra(
            array(
                'installer-paths' => array(
                    $path => array(
                        'type:' . $type,
                    ),
                ),
            )
        );

        $installPath = $installer->getInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * @return mixed[]
     */
    public function loaderCustomInstallPathByTypeDataProvider()
    {
        return array(
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                MuPluginInstaller::TYPE,
                '-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                MuPluginInstaller::TYPE,
                'tests-package/devaloka-muplugin/mu-plugins/package.php/',
            ),
            array(
                'package',
                '{$vendor}-{$name}/{$type}/{$loader}',
                'unknown-type',
                'wp-content/mu-plugins/',
            ),
            array(
                'tests/package',
                '{$vendor}-{$name}/{$type}/{$loader}/',
                'unknown-type',
                'wp-content/mu-plugins/',
            ),
        );
    }

    /**
     * @dataProvider loaderCustomInstallPathByTypeDataProvider
     *
     * @param string $name
     * @param string $path
     * @param string $type
     * @param string $expectedPath
     */
    public function testLoaderCustomInstallPathByType($name, $path, $type, $expectedPath)
    {
        $installer       = $this->createInstaller();
        $package         = $this->createMuPluginPackage($name);
        $consumerPackage = new RootPackage('root/package', '1.0.0', '1.0.0');

        $this->composer->setPackage($consumerPackage);
        $consumerPackage->setExtra(
            array(
                'installer-loader-paths' => array(
                    $path => array(
                        'type:' . $type,
                    ),
                ),
            )
        );

        $installPath = $installer->getLoaderInstallPath($package);

        $this->assertSame($expectedPath, $installPath);
    }

    /**
     * Creates a MU plugin installer.
     *
     * @return MuPluginInstaller
     */
    protected function createInstaller()
    {
        $installer = new MuPluginInstaller($this->io, $this->composer);

        return $installer;
    }

    /**
     * Creates a MU plugin Package.
     *
     * @param string $name
     *
     * @return Package
     */
    protected function createMuPluginPackage($name)
    {
        $package = new Package($name, '1.0.0', '1.0.0');

        $package->setType(MuPluginInstaller::TYPE);

        return $package;
    }
}
