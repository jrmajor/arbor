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
    // Remove case as it removes new line between enum case and method.
    'no_extra_blank_lines' => [
        'tokens' => ['continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'switch', 'throw'],
    ],
])->setCacheFile('.cache/.php-cs-fixer.cache');
