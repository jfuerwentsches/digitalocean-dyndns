# Digital Ocean DynDNS
Simple script consuming Digital Ocean's API to update an A-Record with the floating IP of the host.

# Configuration
The configuration is done using an PHP array in the update_dns.php:

```php
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
```

# Automatic update
To automatically update the settings, you can just use a cronjob on the machine running the script, use `crontab -e`
```bash
*/5		php	/path/to/update_dns.php
```