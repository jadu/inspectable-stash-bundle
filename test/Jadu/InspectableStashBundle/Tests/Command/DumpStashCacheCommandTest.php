<?php

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
            'bind' => null,
            'hasArgument' => null,
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

        $this->output->shouldReceive('write')->andReturnUsing(function ($messages) {
            foreach ((array)$messages as $message) {
                $this->outputText .= $message;
            }
        });
        $this->output->shouldReceive('writeln')->andReturnUsing(function ($messages) {
            foreach ((array)$messages as $message) {
                $this->outputText .= $message . "\n";
            }
        });

        $this->command = new DumpStashCacheCommand($this->inspector);
    }

    public function testPrintsOnlyAMessageWhenNoKeysAreReturnedByTheInspector()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([]);

        $this->command->run($this->input, $this->output);
        
        $this->assertSame("No keys stored by Stash were returned from Memcached\n", $this->outputText);
    }

    public function testPrintsAllCacheEntryKeysAndValues()
    {
        $this->inspector->shouldReceive('getCacheEntries')->andReturn([
            Mockery::mock(CacheEntry::class, [
                'getOriginalKey' => ['this', 'is', 'my', 'first', 'key'],
                'getValue' => 'my first value'
            ]),
            Mockery::mock(CacheEntry::class, [
                'getOriginalKey' => ['this', 'is', 'my', 'second', 'key'],
                'getValue' => ['my' => 'second value']
            ])
        ]);

        $this->command->run($this->input, $this->output);

        $this->assertSame(<<<EOS
Key: this/is/my/first/key
Value: "my first value"
--
Key: this/is/my/second/key
Value: {"my":"second value"}
--

EOS
, $this->outputText);
    }
}
