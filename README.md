
ES for Laravel

### Usage

EsBuilder 有两种模式
1. ES ORM Client (ORM模式)：支持Model映射
2. ES Client (非ORM模式)：支持原生ES

###使用 ES ORM Client

首先创建ORM Model

```php
use Ethansmart\EsBuilder\Model\EsModel;

/**
 * Class AtPerson
 * $host ES IP或URL地址
 * $port ES 端口
 * $index ES 索引名称
 * $type ES 索引 type名称
 * @package Ethan\EsBuilder\Model
 */

class AtPerson extends EsModel
{
    protected $host = "127.0.0.1";
    protected $port = "32800";
    protected $index = "accounts";
    protected $type = "person";
}

```

然后使用Model对ES进行CURD操作

搜索
```php

try {
    $result = AtPerson::build()
              ->select("user")
              ->where("user",'==',"chengluo")
              ->where("title,desc","like","AI")
              ->where("create_time","<","2018-10-05")
              ->get();

} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```

新增
```php

try {
    $id = 5;
    $data = [
       'id'=>$id,
       'params'=>[
            'user'=>'Ethan Cheng',
            'title'=>'AI '.str_random(8),
            'desc'=>'AI '.str_random(12)
       ]
    ];
    $result = AtPerson::build()->create($data);
} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```

更新
```php

try {
    $id = 5;
    $data = [
        'id'=>$id,
        'params'=>[
             'user'=>'Ethan Cheng',
             'title'=>'AI '.str_random(8),
             'desc'=>'AI '.str_random(12)
        ]
    ];
    $result = AtPerson::build()->update($data);
} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```


删除
```php

try {
    $id = 5;
    $result = AtPerson::build()->delete($id);
} catch (\Exception $e) {
    throw $e;
}
     
return $result;

```

###使用 ES Client

首先构建 Client

```php

private $client ;

public function __construct()
{
     $host = "127.0.0.1";
     $port = "32800";
     $this->client = EsClientBuilder::create()
         ->setHosts($host)
         ->setPort($port)
         ->build();
}

```

调用Client中的方法对ES进行CURD操作

```php

$data = [
     'index'=>'accounts',
     'type'=>'person',
     'body'=>[
          "query"=>[
               "bool"=>[
                   "must"=>[
                         "match"=>[
                              "user"=>"ethan"
                         ]
                   ]
               ]
          ]
     ],
];

try {
    $result = $this->client->search($data);
} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}
return $result;

```

其他方法类似

###扩展