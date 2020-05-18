<?php

namespace xYundy\Cloudflare;

use Cloudflare\API\Auth\APIKey as Key;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\ClientException;
use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\DNS as CF_DNS;
use Cloudflare\API\Endpoints\IPs as CF_IPs;
use Cloudflare\API\Endpoints\Zones as CF_ZONES;
use Cloudflare\API\Adapter\Guzzle as Adapter;

class Cloudflare
{
    use Macroable;

    protected $zone;
    protected $zones;
    protected $dns;
    protected $ips;

    public function __construct($email, $api, $zone)
    {
        $key = new Key($email, $api);
        $adapter = new Adapter($key);
        $this->zone = $zone;
        $this->zones = new CF_ZONES($adapter);
        $this->dns = new CF_DNS($adapter);
        $this->ips = new CF_IPs($adapter);
    }


    public function helloWorld() {
        return 'Hello World again!';
    }

    public function listZones() {
        try {
            return $this->zones->listZones();
        } catch (ClientException $e) {
            return false;
        }
    }

    public function getZoneId(string $name) {
        try {
            return $this->zones->getZoneID($name);
        } catch (ClientException $e) {
            return false;
        } catch (EndpointException $e) {
            return false;
        }
    }

    public function getZoneById(string $id)
    {
        try {
            return $this->zones->getZoneById($id);
        } catch (ClientException $e) {
            return false;
        } catch (EndpointException $e) {
            return false;
        }
    }

    public function getZoneByName(string $name) {
        try {
            return $this->zones->getZoneById($this->zones->getZoneID($name));
        } catch (ClientException $e) {
            return false;
        } catch (EndpointException $e) {
            return false;
        }
    
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

    public function getRecordID($zoneID = null, string $type = '', string $name = ''): string {
        return $this->dns->getRecordID($zoneID ?: $this->zone, $type, $name);
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
