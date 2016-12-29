<?php
namespace jfuerwentsches\digitaloceanDyndns;

use DigitalOceanV2\Adapter\BuzzAdapter;
use DigitalOceanV2\DigitalOceanV2;
use DigitalOceanV2\Entity\DomainRecord;
use DigitalOceanV2\Exception\HttpException;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Simple DynDns implementation using Digital Oceans DNS API.
 *
 * @author Johannes Fürwentsches <johannes@fuerwentsches.de>
 */
class DynDns
{

    /**
     * @var BuzzAdapter
     */
    protected $adapter;

    /**
     * @var DigitalOceanV2
     */
    protected $digitalocean;

    /**
     * @var string
     */
    protected $domainName;

    /**
     * @var string
     */
    protected $logger;

    /**
     * DynDns constructor.
     * @param $apiKey Digital Ocean API key
     * @param $domainName Domainname (example.org)
     * @param $logfile Optional path to logfile, leave blank to disable logging
     */
    public function __construct($apiKey, $domainName, $logfile = null)
    {
        $this->domainName = $domainName;
        $this->adapter = new BuzzAdapter($apiKey);
        $this->digitalocean = new DigitalOceanV2($this->adapter);

        if ($logfile != null) {
            $writer = new Stream($logfile);
            $this->logger = new Logger();
            $this->logger->addWriter($writer);
        }
    }

    /**
     * @param string $name Name of the DNS record to update.
     * @return bool
     */
    public function updateDomainRecord($name)
    {
        $domainRecord = $this->digitalocean->domainRecord();
        $domainRecordEntity = $this->getByName($this->domainName, $name);
        $ipAddress = self::getHostIpAddress();

        if ($domainRecordEntity instanceof DomainRecord && $domainRecordEntity->data != $ipAddress) {
            $domainRecord->updateData($this->domainName, $domainRecordEntity->id, $ipAddress);

            if (!empty($this->logger)) {
                $this->logger->info('IP address updated: ' . $ipAddress);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $domainName
     * @param int $domainRecordName
     *
     * @return DomainRecordEntity
     */
    public function getByName($domainName, $domainRecordName)
    {
        $domainRecord = $this->digitalocean->domainRecord();

        try {
            $domainRecords = $domainRecord->getAll($domainName);
        } catch (Exception $exception) {
            if (!empty($this->logger)) {

                if ($exception instanceof HttpException) {
                    $this->logger->err('No domain "' . $domainName . '" found. Original message: ' . $exception->getMessage());
                } elseif ($exception instanceof RequestException) {
                    $this->logger->err('Error sending Request to Digital Ocean API. Original message: ' . $exception->getMessage());
                } else {
                    $this->logger->err($exception->getMessage());
                }

                return false;
            }
        }


        foreach ($domainRecords as $dm) {
            if ($dm->name == $domainRecordName) {
                return $dm;
            }
        }

        if (!empty($this->logger)) {
            $this->logger->warn('No DNS Record "' . $domainRecordName . '" found for domain "' . $domainName . '"');
        }

        return false;
    }

    /**
     * Returns the public IP address of the machine executing the script.
     *
     * @return string
     */
    private static function getHostIpAddress()
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://ipinfo.io/ip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ipAddress = curl_exec($ch);
        $ipAddress = preg_replace('~[\r\n]+~', '', $ipAddress);
        curl_close($ch);

        return $ipAddress;
    }
}