<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_separation' => [
            'skip_unlisted_annotations' => true,
        ],
    ])
    ->setCacheFile(__DIR__.'/var/cache/.php-cs-fixer.cache')
    ->setFinder($finder)
;
