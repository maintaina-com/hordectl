<?php
namespace Horde\Hordectl\Test;

use Horde\Hordectl\YamlWriter;
use PHPUnit\Framework\TestCase;
use \Horde_Yaml_Dumper as Dumper;

class YamlWriterTest extends TestCase
{
    function testNewYamlWriter()
    {
        $mockDumper = $this->createMock(Dumper::class);
        $this->assertInstanceOf(YamlWriter::class, new YamlWriter($mockDumper));
    }
}