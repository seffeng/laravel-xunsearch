<?php  declare(strict_types=1);

namespace Seffeng\LaravelXunsearch\Tests;

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
