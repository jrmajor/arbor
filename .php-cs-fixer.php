<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('bootstrap/cache')
    ->notPath('node_modules')
    ->notPath('storage')
    ->notName('*.blade.php')
    ->notName('_ide_helper*.php')
    ->ignoreVCS(true);

return Major\CS\config($finder, [
    'strict_param' => false,
])->setCacheFile('.cache/.php-cs-fixer.cache');
