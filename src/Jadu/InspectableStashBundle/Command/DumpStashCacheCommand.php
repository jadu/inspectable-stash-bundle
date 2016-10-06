<?php

namespace Jadu\InspectableStashBundle\Command;

use Jadu\InspectableStashBundle\Inspector\CacheEntry;
use Jadu\InspectableStashBundle\Inspector\InspectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this->addOption(
            'with-values',
            'i',
            InputOption::VALUE_NONE,
            'Include values for cache entries along with their keys'
        );
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
            $messages = ['Key: ' . implode('/', $cacheEntry->getOriginalKey())];

            // Include the cache entry's value if requested
            if ($input->getOption('with-values') === true) {
                $messages[] = 'Value: ' . json_encode($cacheEntry->getValue());
            }

            $messages[] = '--';

            /** @var CacheEntry $cacheEntry */
            $output->writeln($messages);
        }
    }
}
