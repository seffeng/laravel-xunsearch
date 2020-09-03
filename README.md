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

# 2、参考扩展包内 config/xunsearch.php 在 .env 文件中添加配置

```

### 目录说明

```
├───config
│       xunsearch.php
├───src
│   │   Xunsearch.php
│   │   XunsearchServiceProvider.php
│   └───Facades
│           Xunsearch.php
```

### 示例

```php
/**
 * 参考 tests/XunsearchTest.php
 */

class SiteController extends Controller
{
    public function test()
    {
        try {
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