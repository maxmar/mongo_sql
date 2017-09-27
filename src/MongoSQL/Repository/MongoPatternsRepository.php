<?php

namespace MongoSQL\Repository;

class MongoPatternsRepository
{
    const MAIN_PATTERN = '/^SELECT\s+(.+?)\s+FROM\s+(\w+?)(\s+WHERE\s+(.+?))?(\s+ORDER\s+BY\s+(.+?))?(\s+SKIP\s+(\d+?))?(\s+LIMIT\s+(\d+?))?;$/';

    public static $patternParams = [
        [
            'index' => 1,
            'is_required' => true,
            'method' => 'prepareFields',
            'key' => 'fields',
            'params' => [
                'pattern' => '/^((\*|\w+|\w+\.\w+|\w+\.\*)(\s*,\s*(\*|\w+|\w+\.\w+|\w+\.\*))*)$/',
                'all_fields_marker' => '*',
                'all_subfields_marker' => '.*',
                'show_field_marker' => 1,
            ]
        ],
        [
            'index' => 2,
            'is_required' => true,
            'method' => 'prepareCollection',
            'key' => 'collection',
        ],
        [
            'index' => 4,
            'is_required' => false,
            'method' => 'prepareWhere',
            'key' => 'where',
            'params' => [
                'pattern' => '/^(\w+|\w+\.\w+)(\s*(=|<>|>|>=|<|<=)(\w+|\w+\.\w+)\s*)(\w+|\w+\.\w+)$/',
//                'field_name_index' => 2,
//                'sort_type_index' => 4,
                'logical_operators' => [ // sort by priority
                    [
                        'sql' => 'OR',
                        'mongo' => '$or',
                    ],
                    [
                        'sql' => 'AND',
                        'mongo' => '$and',
                    ],
                ]
            ]
        ],
        [
            'index' => 6,
            'is_required' => false,
            'method' => 'prepareOrderBy',
            'key' => 'sort',
            'params' => [
                'pattern' => '/^((\w+|\w+\.\w+)(\s+(ASC|DESC))?)$/',
                'field_name_index' => 2,
                'sort_type_index' => 4,
                'sort_types' => [
                    'DESC' => -1,
                    'ASC' => 1,
                ]
            ]
        ],
        [
            'index' => 8,
            'is_required' => false,
            'method' => 'prepareSkip',
            'key' => 'skip',
        ],
        [
            'index' => 10,
            'is_required' => false,
            'method' => 'prepareLimit',
            'key' => 'limit',
        ],
    ];
}