<?php

namespace Ethansmart\EsBuilder\Model;

use Ethansmart\EsBuilder\Builder\EsClientBuilder;
use Log;

class EsModel
{
    protected $index = null;
    protected $type = null ;
    protected $params = null ;
    protected $query = null ;
    protected $id = null ;
    protected $selectParams = null ;
    protected $sourceStatus = true ;
    protected $host = null ;
    protected $port = null ;
    protected $with = [];
    protected $perPage = 15;
    protected $client = null ;

    public function __construct()
    {
        $this->client = EsClientBuilder::create()
            ->setHosts($this->host)
            ->setPort($this->port)
            ->setOrmStatus(true)
            ->build();
    }

    public static function build()
    {
        return new static();
    }

    public function select(...$selectParams)
    {
        if (!empty($selectParams)) {
            $this->setSourceStatus($selectParams);
            Log::info("selectParams :".json_encode($selectParams));
        }

        return $this;
    }


    public function where($firstParam, $option, $secondParam = null)
    {
        if (in_array($option, array("=", ">", ">=", "<", "<=", "like", "=="))) {
            switch ($option) {
                case "=":
                    $this->params[] = [
                        "match"=>[
                            $firstParam => $secondParam
                        ]
                    ];
                    break;

                case "like":
                    $firstParam = explode(',' ,$firstParam);
                    $this->params[] = [
                        "multi_match" =>[
                            "query"=> $secondParam,
                            "fields"=> $firstParam
                        ]
                    ];
                    break;

                case "==":
                    $this->params[] = [
                        "match_phrase"=>[
                            $firstParam => $secondParam
                        ]
                    ];
                    break;

                case ">":
                    $this->params[] = [
                        "range" =>[
                            $firstParam => ["gt" => $secondParam]
                        ]
                    ];
                    break;

                case ">=":
                    $this->params[] = [
                        "range" =>[
                            $firstParam => ["gte" => $secondParam]
                        ]
                    ];
                    break;

                case "<":
                    $this->params[] = [
                        "range" =>[
                            $firstParam => ["lt" => $secondParam]
                        ]
                    ];
                    break;

                case "<=":
                    $this->params[] = [
                        "range" =>[
                            $firstParam => ["lte" => $secondParam]
                        ]
                    ];
                    break;
            }
        }else{
            $this->params[] = [
                "match"=>[
                    $firstParam => $option
                ]
            ];
        }

        Log::info("params:",$this->params);
        return $this ;
    }


    public function get()
    {
        try {
            $this->client->getParamsBuilder()
                ->setMethod("search")
                ->setIndex($this->index)
                ->setType($this->type)
                ->setBody($this->getQuery());

            $result = $this->client->search();
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }

    public function delete($id)
    {
        try {
            $this->client->getParamsBuilder()
                ->setMethod("delete")
                ->setIndex($this->index)
                ->setType($this->type)
                ->setID($id);

            $result = $this->client->delete();
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function update($data)
    {
        try {
            $this->client->getParamsBuilder()
                ->setMethod("update")
                ->setIndex($this->index)
                ->setType($this->type)
                ->setID($data['id'])
                ->setBody($data['params']);

            $result = $this->client->update();
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function insert($data)
    {
        try {
            $this->client->getParamsBuilder()
                ->setMethod("create")
                ->setIndex($this->index)
                ->setType($this->type)
                ->setID($data['id'])
                ->setBody($data['params']);

            $result = $this->client->create();
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }
    
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return "";
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function setSourceStatus($status)
    {
        if ($status !== true && !empty($status)) {
            $this->sourceStatus = $status;
        }
    }

    public function getQuery()
    {
        $this->query = [
            "_source"=>$this->sourceStatus,
            "query"=>[
                "bool"=>[
                    "must"=>[
                        $this->params
                    ]
                ]
            ]
        ];

        return $this->query;
    }
}