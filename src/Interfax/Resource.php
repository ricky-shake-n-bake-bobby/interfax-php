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

/**
 * Class Resource
 *
 * Base Resource Class to be used for resource classes that are represented by specific endpoints on the API.
 *
 * @package Interfax
 */
abstract class Resource
{
    /**
     * @var GenericFactory
     */
    protected $factory;

    /**
     * @var Client
     */
    protected $client;
    /**
     * Base URI used for carrying out actions on the resource.
     *
     * @var string
     */
    protected $resource_uri;
    /**
     * Stores the internal properties of the resource.
     *
     * @var array
     */
    protected $record = [];

    /**
     * Should be overridden in inheriting class
     *
     * @var
     */
    protected static $resource_uri_stem;

    /**
     * Resource constructor.
     *
     * @param Client $client
     * @param $id
     * @param array $definition
     * @param GenericFactory|null $factory
     */
    public function __construct(Client $client, $id, $definition = [], GenericFactory $factory = null)
    {
        $this->client = $client;

        $this->resource_uri = static::$resource_uri_stem . $id;

        foreach ($definition as $k => $v) {
            $this->record[$k] = $v;
        }

        if ($factory === null) {
            $factory = new GenericFactory();
        }
        $this->factory = $factory;
    }
}