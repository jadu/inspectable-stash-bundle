<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/src');

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'no_blank_lines_after_class_opening',
        'blankline_after_open_tag',
        'whitespacy_lines',
        'unused_use'
    ])
    ->finder($finder);
