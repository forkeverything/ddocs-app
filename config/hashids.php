<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt' => 'file collecting made easy',
            'length' => '12',
            'alphabet' => '0123456789abcdef',
        ],

        'recipient' => [
            'salt' => 'keeping our recipients private',
            'length' => '12',
            'alphabet' => '0123456789abcdefghijklmnopqrstuvwxyz',
        ],

        'checklist' => [
            'salt' => 'super secret checklists are the best',
            'length' => '12',
            'alphabet' => '0123456789abcdefghijklmnopqrstuvwxyz',
        ],

        'file-request' => [
            'salt' => 'hope nobody finds this file',
            'length' => '12',
            'alphabet' => '0123456789abcdefghijklmnopqrstuvwxyz',
        ],

        'note' => [
            'salt' => 'notes keep everybody on the same page',
            'length' => '8',
            'alphabet' => '0123456789abcdefghijklmnopqrstuvwxyz',
        ]

    ],

];
