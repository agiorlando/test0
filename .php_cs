<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('tests')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
    ))
    ->setFinder($finder)
    ;