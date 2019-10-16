<?php

declare(strict_types=1);

namespace LoomTest\Config;

use Loom\Config\ConfigGather;
use Loom\Config\Exception\InvalidConfigProcessorException;
use Loom\Config\Exception\InvalidConfigProviderException;
use LoomTest\Config\Resources\BarConfigProvider;
use LoomTest\Config\Resources\FooConfigProvider;
use LoomTest\Config\Resources\FooPostProcessor;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use stdClass;

use function file_exists;
use function var_export;

class ConfigGatherTest extends TestCase
{
    public function testConfigGatherRisesExceptionIfProviderClassDoesNotExist()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigGather(['NonExistentConfigProvider']);
    }

    public function testConfigGatherRisesExceptionIfProviderIsNotCallable()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigGather([stdClass::class]);
    }

    public function testConfigGatherMergesConfigFromProviders()
    {
        $gather = new ConfigGather([FooConfigProvider::class, BarConfigProvider::class]);
        $config = $gather->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bat'], $config);
    }

    public function testProviderCanBeClosure()
    {
        $gather = new ConfigGather([
            function () {
                return ['foo' => 'bar'];
            },
        ]);
        $config = $gather->getMergedConfig();
        $this->assertEquals(['foo' => 'bar'], $config);
    }

    public function testProviderRisesExceptionIfClosureReturnNonArray()
    {
        $this->expectException(InvalidConfigProviderException::class);
        $gather = new ConfigGather([
            function () {
                return 'foo';
            },
        ]);
    }

    public function testProviderCanBeGenerator()
    {
        $gather = new ConfigGather([
            function () {
                yield ['foo' => 'bar'];
                yield ['baz' => 'bat'];
            },
        ]);
        $config = $gather->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'baz' => 'bat'], $config);
    }

    public function testConfigGatherCanCacheConfig()
    {
        vfsStream::setup(__FUNCTION__);
        $cacheFile = vfsStream::url(__FUNCTION__) . '/expressive_config_loader';
        new ConfigGather([
            function () {
                return ['foo' => 'bar', ConfigGather::ENABLE_CACHE => true];
            }
        ], $cacheFile);
        $this->assertTrue(file_exists($cacheFile));
        $cachedConfig = include $cacheFile;
        $this->assertInternalType('array', $cachedConfig);
        $this->assertEquals(['foo' => 'bar', ConfigGather::ENABLE_CACHE => true], $cachedConfig);
    }

    public function testConfigGatherCanLoadConfigFromCache()
    {
        $expected = [
            'foo' => 'bar',
            ConfigGather::ENABLE_CACHE => true,
        ];

        $root = vfsStream::setup(__FUNCTION__);
        vfsStream::newFile('expressive_config_loader')
            ->at($root)
            ->setContent('<' . '?php return ' . var_export($expected, true) . ';');
        $cacheFile = vfsStream::url(__FUNCTION__ . '/expressive_config_loader');

        $gather = new ConfigGather([], $cacheFile);
        $mergedConfig = $gather->getMergedConfig();

        $this->assertInternalType('array', $mergedConfig);
        $this->assertEquals($expected, $mergedConfig);
    }

    public function testConfigGatherRisesExceptionIfProcessorClassDoesNotExist()
    {
        $this->expectException(InvalidConfigProcessorException::class);
        new ConfigGather([], null, ['NonExistentConfigProcessor']);
    }

    public function testConfigGatherRisesExceptionIfProcessorIsNotCallable()
    {
        $this->expectException(InvalidConfigProcessorException::class);
        new ConfigGather([], null, [stdClass::class]);
    }

    public function testProcessorCanBeClosure()
    {
        $gather = new ConfigGather([], null, [
            function (array $config) {
                return $config + ['processor' => 'closure'];
            },
        ]);

        $config = $gather->getMergedConfig();
        $this->assertEquals(['processor' => 'closure'], $config);
    }

    public function testConfigGatherCanPostProcessConfiguration()
    {
        $gather = new ConfigGather([
            function () {
                return ['foo' => 'bar'];
            },
        ], null, [new FooPostProcessor]);
        $mergedConfig = $gather->getMergedConfig();

        $this->assertEquals(['foo' => 'bar', 'post-processed' => true], $mergedConfig);
    }
}
