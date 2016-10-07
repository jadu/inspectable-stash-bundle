<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle\Tests\Command;

use Jadu\InspectableStashBundle\Command\DumpStashCacheCommand;
use Jadu\InspectableStashBundle\Inspector\CacheEntry;
use Jadu\InspectableStashBundle\Inspector\InspectorInterface;
use Mockery;
use Mockery\Mock;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DumpStashCacheCommandTest
 *
 * @author Jadu Ltd.
 */
class DumpStashCacheCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DumpStashCacheCommand
     */
    private $command;

    /**
     * @var Mock|InputInterface
     */
    private $input;

    /**
     * @var Mock|InspectorInterface
     */
    private $inspector;

    /**
     * @var Mock|OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $outputText;

    public function setUp()
    {
        $this->helperSet = Mockery::mock(HelperSet::class);
        $this->input = Mockery::mock(InputInterface::class, [
            'addOption' => null,
            'bind' => null,
            'hasArgument' => false,
            'hasOption' => false,
            'isInteractive' => true,
            'validate' => true
        ]);
        $this->inspector = Mockery::mock(InspectorInterface::class, [
            'getCacheEntries' => []
        ]);
        $this->output = Mockery::mock(OutputInterface::class, [
            'getFormatter' => Mockery::mock(OutputFormatterInterface::class, [
                'setStyle' => null
            ])
        ]);
        $this->outputText = '';

        $this->input->shouldReceive('getOption')->andReturnUsing(function ($option) {
            if ($option === 'with-values') {
                return false;
            }

            return null;
        })->byDefault();

        $this->output->shouldReceive('write')->andReturnUsing(function ($messages) {
            foreach ((array)$messages as $message) {
                $this->outputText .= $message;
            }
        })->byDefault();
        $this->output->shouldReceive('writeln')->andReturnUsing(function ($messages) {
            foreach ((array)$messages as $message) {
                $this->outputText .= $message . "\n";
            }
        })->byDefault();

        $this->command = new DumpStashCacheCommand($this->inspector);
    }

    public function testPrintsOnlyAMessageWhenNoKeysAreReturnedByTheInspector()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([]);

        $this->command->run($this->input, $this->output);
        
        $this->assertSame("No keys stored by Stash were returned from Memcached\n", $this->outputText);
    }

    public function testPrintsAllCacheEntryKeysWithoutValuesByDefault()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/first/key',
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/second/key',
                'getValue' => ['my' => 'second value']
            ])
        ]);

        $this->command->run($this->input, $this->output);

        $expectedStdout = <<<EOS
Key: this/is/my/first/key
--
Key: this/is/my/second/key
--

EOS;
        $this->assertSame($expectedStdout, $this->outputText);
    }

    public function testPrintsAllCacheEntryKeysWithValuesWhenOptionSelected()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/first/key',
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/second/key',
                'getValue' => ['my' => 'second value']
            ])
        ]);
        $this->input->shouldReceive('getOption')->with('with-values')->andReturn(true);

        $this->command->run($this->input, $this->output);

        $expectedStdout = <<<EOS
Key: this/is/my/first/key
Value: "my first value"
--
Key: this/is/my/second/key
Value: {"my":"second value"}
--

EOS;
        $this->assertSame($expectedStdout, $this->outputText);
    }

    public function testPrintsOnlyMatchingCacheEntryKeysWithoutValuesWhenGrepOptionGiven()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/first/key',
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/second/key',
                'getValue' => ['my' => 'second value']
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/third/key',
                'getValue' => ['my' => 'third value']
            ])
        ]);
        $this->input->shouldReceive('getOption')->with('with-values')->andReturn(false);
        $this->input->shouldReceive('hasOption')->with('grep')->andReturn(true);
        $this->input->shouldReceive('getOption')->with('grep')->andReturn('my/(second|third)');

        $this->command->run($this->input, $this->output);

        $expectedStdout = <<<EOS
Key: this/is/my/second/key
--
Key: this/is/my/third/key
--

EOS;
        $this->assertSame($expectedStdout, $this->outputText);
    }

    public function testPrintsOnlyMatchingCacheEntryKeysButWithValuesWhenValueAndGrepOptionsGiven()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/first/key',
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/second/key',
                'getValue' => ['my' => 'second value']
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/third/key',
                'getValue' => ['my' => 'third value']
            ])
        ]);
        $this->input->shouldReceive('getOption')->with('with-values')->andReturn(true);
        $this->input->shouldReceive('hasOption')->with('grep')->andReturn(true);
        $this->input->shouldReceive('getOption')->with('grep')->andReturn('my/(second|third)');

        $this->command->run($this->input, $this->output);

        $expectedStdout = <<<EOS
Key: this/is/my/second/key
Value: {"my":"second value"}
--
Key: this/is/my/third/key
Value: {"my":"third value"}
--

EOS;
        $this->assertSame($expectedStdout, $this->outputText);
    }

    public function testGrepOptionSupportsUseOfTheRegexDelimiterInPattern()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/@/key',
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/second/key',
                'getValue' => ['my' => 'second value']
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKeyString' => 'this/is/my/third/key',
                'getValue' => ['my' => 'third value']
            ])
        ]);
        $this->input->shouldReceive('getOption')->with('with-values')->andReturn(false);
        $this->input->shouldReceive('hasOption')->with('grep')->andReturn(true);
        $this->input->shouldReceive('getOption')->with('grep')->andReturn('my/@/');

        $this->command->run($this->input, $this->output);

        $expectedStdout = <<<EOS
Key: this/is/my/@/key
--

EOS;
        $this->assertSame($expectedStdout, $this->outputText);
    }
}
