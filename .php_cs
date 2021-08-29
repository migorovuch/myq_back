<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setFinder(PhpCsFixer\Finder::create()->files()->in(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests'
        ]
    )->name('*.php'));
