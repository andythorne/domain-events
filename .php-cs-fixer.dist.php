<?php

$finder = (new PhpCsFixer\Finder())
    ->exclude('vendor')
    ->exclude('tests/Functional/var')
    ->in('src')
    ->in('tests/Unit')
    ->in('tests/Functional/Doctrine');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR2' => true,
        'ordered_imports' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_class_elements' => true,
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'general_phpdoc_annotation_remove' => ['annotations ' => ['author', 'package']],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
        ],
    ])
    ->setFinder($finder);
