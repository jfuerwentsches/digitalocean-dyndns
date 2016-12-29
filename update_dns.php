<?php
use jfuerwentsches\digitaloceanDyndns\DynDns;

require __DIR__ . '/vendor/autoload.php';

/**
 * Configure Domains and Hostnames to be updated with the public IP address of the machine running this script.
 */
$config = array('logfile' => 'dyndns.log', // if you leave the filename blank, logging will be disabled
                'api_key' => '<your_digital_ocean_api_key>',
                'domains' => array(
                    'example.org' => array(
                        'subdomain',
                        'sub.subdamin',
                        '*.subdomain'
                    ),
                    'example.com' => array(
                        'my'
                    )
                )
            );



/* Update all configured domains and hostnames. */
foreach ($config['domains'] as $domain => $hostnames) {
    $dyndns = new DynDns($config['api_key'], $domain, $config['logfile']);

    foreach ($hostnames as $hostname) {
        $dyndns->updateDomainRecord($hostname);
    }
}