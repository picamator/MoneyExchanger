<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'logger' => array(
        'writers' => array(
            'stream' => array(
                'name' => 'stream',
                'options' => array(
                    'stream' => './data/logs/application.log',
                    'filters' => array(
                        'priority' => array(
                            'name' => 'priority',
                            'options' => array(
                                'priority' => 4 // WARN
                            )
                        ),
                        'suppress' => array(
                            'name' => 'suppress',
                            'options' => array(
                                'suppress' => false
                            )
                        )
                    ),
                    'formatter' => array(
                        'name' => 'simple',
                        'options' => array(
                            'dateTimeFormat' => 'Y-m-d H:i:s'
                        )
                    )
                )
            )
        )
    ),
    
    'phpSettings' => array(
        'display_errors'  => 0,
        'display_startup_errors' => 0,
        'date.timezone' => 'Europe/Warsaw'
    ),
    
    'yahooClient' => array(
        'endpoint' => 'http://download.finance.yahoo.com',
        'convert' => array(
            'from' => 'RUB',
            'to'   => 'PLN'
        )
    )
);
