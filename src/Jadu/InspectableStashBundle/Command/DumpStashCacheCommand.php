<?php

namespace Jadu\InspectableStashBundle\Command;

use Jadu\InspectableStashBundle\Inspector\CacheEntry;
use Jadu\InspectableStashBundle\Inspector\InspectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DumpStashCacheCommand
 *
 * @author Jadu Ltd.
 */
class DumpStashCacheCommand extends Command
{
    /**
     * @var InspectorInterface
     */
    private $inspector;

    /**
     * @param InspectorInterface $inspector
     */
    public function __construct(InspectorInterface $inspector)
    {
        parent::__construct();

        $this->inspector = $inspector;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('dump:stash:cache');
        $this->setDescription('Dumps the data currently stored in the Stash cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheEntries = $this->inspector->getCacheEntries();

        if (count($cacheEntries) === 0) {
            $output->writeln('No keys stored by Stash were returned from Memcached');
            return;
        }

        foreach ($cacheEntries as $cacheEntry) {
            /** @var CacheEntry $cacheEntry */
            $output->writeln([
                'Key: ' . implode('/', $cacheEntry->getOriginalKey()),
                'Value: ' . json_encode($cacheEntry->getValue()),
                '--'
            ]);
        }
    }
}
