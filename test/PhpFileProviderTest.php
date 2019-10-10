<?php

declare(strict_types=1);

namespace LoomTest\Config;

use Loom\Config\PhpFileProvider;
use Loom\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class PhpFileProviderTest extends TestCase
{
    public function testProviderLoadsConfigFromFiles()
    {
        $provider = new PhpFileProvider(__DIR__ . '/Resources/config/{{,*.}global,{,*.}local}.php');
        $merged = [];
        foreach ($provider() as $item) {
            $merged = ArrayUtil::mergeArray($merged, $item);
        }
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], $merged);
    }
}
