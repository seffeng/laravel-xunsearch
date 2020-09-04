## Laravel Xunsearch

### 安装

```shell
$ composer require seffeng/laravel-xunsearch
```

##### Laravel

```shell
# 1、生成配置文件
$ php artisan vendor:publish --tag="xunsearch"

# 2、修改配置文件 /config/xunsearch.php 或 /.env，建议通过修改 .env 实现配置，

```

##### Lumen

```php
# 1、将以下代码段添加到 /bootstrap/app.php 文件中的 Providers 部分
$app->register(Seffeng\LaravelXunsearch\XunsearchServiceProvider::class);

# 2、复制扩展包内 config/xunsearch.php 到项目 config 目录，修改项目字段， 在 .env 文件中添加配置

```

### 目录说明

```
├───config
│       xunsearch.php
├───src
│   │   Xunsearch.php
│   │   XunsearchServiceProvider.php
|   ├───Exceptions
|   |       XunsearchException.php
│   └───Facades
│           Xunsearch.php
```

### 示例

```php
/**
 * 参考 tests/XunsearchTest.php
 */
use PHPUnit\Framework\TestCase;
use Seffeng\LaravelXunsearch\Facades\Xunsearch;

class XunsearchTest extends TestCase
{
    /**
     *
     * @author zxf
     * @date    2020年4月17日
     * @throws \Exception
     */
    public function testSearch()
    {
        try {
            // 添加文档
            $data = ['id' => 1, 'name' => '李白']; // 或二维数组 [['id' => 1, 'name' => '李白'], ['id' => 2, 'name' => '杜甫']]
            Xunsearch::addIndex($data);
            // 或非默认项目库
            // 注意：使用 setDatabase($db) 后，再使用 Xunsearch::{$method} 也为新设置的$db
            Xunsearch::setDatabase('author')->addIndex($data);
            
            // 修改文档
            $data = ['id' => 1, 'name' => '李白，字太白'];
            Xunsearch::updateIndex($data);
            
            // 删除文档
            Xunsearch::delIndex([1, 2]);
            
            // 清空文档
            Xunsearch::cleanIndex();
            
            // 平滑重建索引
            Xunsearch::beginRebuild();
            Xunsearch::addIndex($data);
            Xunsearch::endRebuild();
            
            // 搜索
            // Xunsearch::setFuzzy(true|false);  // 是否模糊搜索
            // Xunsearch::setLimit(int 10);  // 每页数量
            // Xunsearch::setOffset(int 0); // 偏移量，与 setPage 不同时使用
            // Xunsearch::setPage(int 1);   // 当前页，改变偏移量，与 setOffset 不同时使用
            $result = Xunsearch::search('李白');
            /**
            Array
            (
                [time] => 0.003633975982666             // 搜索时长[s]
                [data] => Array                         // 结果
                    (
                        [0] => XSDocument Object
                            (
                                [_data:XSDocument:private] => Array
                                    (
                                        [id] => 1
                                        [name] => 李白，字太白
                                        [255] =>
                                    )

                                [_terms:XSDocument:private] =>
                                [_texts:XSDocument:private] =>
                                [_charset:XSDocument:private] => UTF-8
                                [_meta:XSDocument:private] => Array
                                    (
                                        [docid] => 1
                                        [rank] => 1
                                        [ccount] => 0
                                        [percent] => 100
                                        [weight] => 0.40546509623528
                                    )

                            )

                    )

                [page] => Array
                    (
                        [totalCount] => 1               // 结果数量
                        [pageCount] => 1                // 总页数
                        [currentPage] => 1              // 当前页
                        [perPage] => 10                 // 每页数量
                    )

            )
            */
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
    
```

## 项目依赖

| 依赖               | 仓库地址                               | 备注 |
| :----------------- | :------------------------------------- | :--- |
| hightman/xunsearch | https://github.com/hightman/xs-sdk-php | 无   |

### 备注

1、测试脚本 tests/XunsearchTest.php 仅作为示例供参考。

2、xunsearch [安装](https://hub.docker.com/r/seffeng/xunsearch)。

```shell
# docker 安装
$ docker pull seffeng/xunsearch

# 启动
$ docker run -d seffeng/xunsearch
```

3、xunsearch [官方介绍](http://xunsearch.com/doc/php/guide/start.installation)。