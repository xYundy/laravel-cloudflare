<?php

namespace Novalis\Cloudflare;

use Cloudflare\API\Auth\APIKey as Key;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\ClientException;
use Cloudflare\API\Endpoints\DNS as CF_DNS;
use Cloudflare\API\Endpoints\IPs as CF_IPs;
use Cloudflare\API\Adapter\Guzzle as Adapter;

class Cloudflare
{
    use Macroable;

    protected $zone;
    protected $dns;
    protected $ips;

    public function __construct($email, $api, $zone)
    {
        $key = new Key($email, $api);
        $adapter = new Adapter($key);
        $this->zone = $zone;
        $this->dns = new CF_DNS($adapter);
        $this->ips = new CF_IPs($adapter);
    }

    /*
     * DNS Queries
     */
    public function addRecord($name, $content = null, $type = 'A', $ttl = 0, $proxied = true, $zone = null)
    {
        if ($content == null && $type = 'A') {
            $content = $_SERVER['SERVER_ADDR'];
        }

        try {
            return $this->dns->addRecord($zone ?: $this->zone, $type, $name, $content, $ttl, $proxied);
        } catch (ClientException $e) {
            return false;
        }
    }

    public function listRecords($info = false, $page = 0, $perPage = 20, $order = '', $direction = '', $type = '', $name = '', $content = '', $match = 'all', $zone = null)
    {
        $records = $this->dns->listRecords($zone ?: $this->zone, $type, $name, $content, $page, $perPage, $order, $direction);

        if ($info) {
            return $records;
        }

        return collect($records->result);
    }

    public function getRecordDetails($recordId, $zone = null)
    {
        return $this->dns->getRecordDetails($zone ?: $this->zone, $recordId);
    }

    public function updateRecordDetails($recordId, array $details, $zone = null)
    {
        return $this->dns->updateRecordDetails($zone ?: $this->zone, $recordId, $details);
    }

    public function deleteRecord($recordId, $zone = null)
    {
        return $this->dns->deleteRecord($zone ?: $this->zone, $recordId);
    }

    /*
     * IP Queries
     */
    public function listIPs()
    {
        return $this->ips->listIPs();
    }
}
