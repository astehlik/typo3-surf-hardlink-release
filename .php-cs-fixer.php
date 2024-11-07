<?php

$finder = PhpCsFixer\Finder::create()
    ->in(
        [
            'src',
            'tests',
        ]
    );

$rules = include __DIR__ . '/vendor/de-swebhosting/php-codestyle/PhpCsFixer/PerCsDefaultRules.php';
$rules['no_superfluous_phpdoc_tags'] = ['allow_mixed' => true, 'remove_inheritdoc' => false];

$config = new PhpCsFixer\Config();
return $config->setRules($rules)
    ->setRiskyAllowed(true)
    ->setFinder($finder);
