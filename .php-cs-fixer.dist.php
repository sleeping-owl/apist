<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'cast_spaces' => ['space' => 'none'],
        'trailing_comma_in_multiline' => true,
        'phpdoc_single_line_var_spacing' => true,
        'blank_line_after_opening_tag' => false,
        'array_indentation' => true,
        'trim_array_spaces' => true,
        'yoda_style' => ['identical' => false],
        'phpdoc_add_missing_param_annotation' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls'
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => [
                'class',
                'const',
                'function',
            ],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'none',
                'method' => 'one',
                'property' => 'none',
                'trait_import' => 'one',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'return',
                'throw',
                'try',
            ],
        ],
    ])
    ;
