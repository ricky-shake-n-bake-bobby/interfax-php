<?php
/**
 * Interfax
 *
 * (C) InterFAX, 2016
 *
 * @package interfax/interfax
 * @author Interfax <dev@interfax.net>
 * @author Mike Smith <mike.smith@camc-ltd.co.uk>
 * @copyright Copyright (c) 2016, InterFAX
 * @license MIT
 */

namespace Interfax;

use GuzzleHttp\Psr7\Request;
use Interfax\Exception\RequestException;
use Interfax\Outbound\Fax;

class Outbound
{
    /**
     * @var GenericFactory
     */
    private $factory;

    protected $client;

    public function __construct(Client $client, GenericFactory $factory = null)
    {
        $this->client = $client;
        if ($factory === null) {
            $factory = new GenericFactory();
        }
        $this->factory = $factory;
    }



    /**
     * @param array $definitions
     * @return Fax[]
     * @throws \InvalidArgumentException
     */
    protected function createFaxes($definitions)
    {
        $res = [];
        foreach ($definitions as $f_data) {
            if (array_key_exists('id', $f_data)) {
                $res[] = $this->factory->instantiateClass('Interfax\Outbound\Fax', [$this->client, $f_data['id'], $f_data]);
            }
            else {
                throw new \InvalidArgumentException('No id attribute found in fax definition');
            }
        }

        return $res;
    }

    /**
     * @param array $ids
     * @return Fax[]
     * @throws \InvalidArgumentException
     * @throws RequestException
     */
    public function completed($ids = [])
    {
        $params = [];
        if (count($ids) === 0) {
            throw new \InvalidArgumentException('Must provide at least one id for completed request');
        }

        $params['query'] = ['ids' => implode(',', $ids)];

        $json = $this->client->get('/outbound/faxes/completed', $params);
        return $this->createFaxes($json);
    }

    /**
     * @param $params
     * @return Outbound\Fax[]
     * @throws \InvalidArgumentException
     * @throws RequestException
     */
    public function recent($params)
    {
        return $this->createFaxes($this->client->get('/outbound/faxes', ['query' => $params]));
    }


    /**
     * @param $id
     * @param null $fax_number
     * @return mixed
     * @throws RequestException
     */
    public function resend($id, $fax_number = null)
    {
        $fax = $this->factory->instantiateClass('Interfax\Outbound\Fax', [$this->client, $id]);

        return $fax->resend($fax_number);
    }

    /**
     * @param array $params
     * @return Outbound\Fax[]
     * @throws \InvalidArgumentException
     * @throws RequestException
     */
    public function search($params = [])
    {
        return $this->createFaxes($this->client->get('/outbound/search', ['query' => $params]));
    }
}