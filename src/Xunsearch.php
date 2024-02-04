<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelXunsearch;

use Seffeng\LaravelXunsearch\Exceptions\XunsearchException;

/**
 *
 * @author zxf
 * @date   2020年9月3日
 */
class Xunsearch
{
    /**
     *
     * @var string
     */
    private $database;

    /**
     *
     * @var \XS
     */
    private $search;

    /**
     *
     * @var array
     */
    private $config;

    /**
     *
     * @var boolean
     */
    private $fuzzy = true;

    /**
     *
     * @var integer
     */
    private $limit = 10;

    /**
     *
     * @var integer
     */
    private $offset = 0;

    /**
     *
     * @var boolean
     */
    private $flushIndex = true;

    /**
     *
     * @var string|array
     */
    private $range;

    /**
     *
     * @var array
     */
    private $weight;

    /**
     *
     * @var array
     */
    private $sort;

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->database = $config['default'];
        isset($config['flushIndex']) && $this->flushIndex = $config['flushIndex'];
        $this->setSearch();
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $query
     * @param bool $saveHighlight
     * @return \XSDocument
     */
    public function search(string $query, bool $saveHighlight = true)
    {
        $startTime = microtime(true);
        $search = $this->getSearch();
        $search->setFuzzy($this->getFuzzy())->setQuery($query);

        if ($this->getRange()) foreach ($this->getRange() as $range) {
            $search->addRange($range['0'], $range['1'], $range['2']);
        }
        if ($this->getWeight()) foreach ($this->getWeight() as $weight) {
            $search->addWeight($weight['0'], $weight['1'], $weight['2']);
        }

        $totalCount = $search->count();
        $search->setLimit($this->getLimit(), $this->getOffset());

        if ($this->getSort()) {
            $search->setSort($this->getSort()['0'], $this->getSort()['1'], $this->getSort()['2']);
        }

        $items = $search->search(null, $saveHighlight);
        $searchCost = microtime(true) - $startTime;

        return [
            'time' => $searchCost,
            'data' => $items,
            'page' => [
                'totalCount' => $totalCount,
                'pageCount' => $totalCount > $this->getLimit() ? ceil($totalCount / $this->getLimit()) : ($totalCount > 0 ? 1 : 0),
                'currentPage' => $this->getPage(),
                'perPage' => $this->getLimit(),
            ],
        ];
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param array $items
     * @throws XunsearchException
     * @return \XSIndex
     */
    public function addIndex(array $items)
    {
        try {
            if (isset($items[0]) && is_array($items[0])) {
                foreach ($items as $item) {
                    $doc = new \XSDocument();
                    $doc->setFields($item);
                    $this->getIndex()->add($doc);
                }
            } else {
                $doc = new \XSDocument();
                $doc->setFields($items);
                $this->getIndex()->add($doc);
            }
            if ($this->flushIndex) {
                $this->getIndex()->flushIndex();
            }
            return $this->getIndex();
        } catch (\Exception $e) {
            throw new XunsearchException('索引添加失败！');
        }
    }

    /**
     *
     * @author zxf
     * @date    2020年9月3日
     * @param array $item
     * @throws XunsearchException
     * @return boolean
     */
    public function updateIndex(array $item)
    {
        try {
            $doc = new \XSDocument;
            $doc->setFields($item);
            $this->getIndex()->update($doc);

            if ($this->flushIndex) {
                $this->getIndex()->flushIndex();
            }
            return true;
        } catch (\Exception $e) {
            throw new XunsearchException('索引修改失败！');
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param array $idItems
     * @throws XunsearchException
     * @return \XSIndex
     */
    public function delIndex(array $idItems)
    {
        try {
            $this->getIndex()->del($idItems);
            if ($this->flushIndex) {
                $this->getIndex()->flushIndex();
            }
            return $this->getIndex();
        } catch (\Exception $e) {
            throw new XunsearchException('索引删除失败！');
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @throws XunsearchException
     * @return \XSIndex
     */
    public function cleanIndex()
    {
        try {
            $this->getIndex()->clean();
            return $this->getIndex();
        } catch (\Exception $e) {
            throw new XunsearchException('索引清空失败！');
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param bool $fuzzy
     * @return static
     */
    public function setFuzzy(bool $fuzzy = true)
    {
        $this->fuzzy = $fuzzy;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年12月17日
     * @param string|array $field
     * @param boolean $asc
     * @param boolean $relevance
     * @return \Seffeng\LaravelXunsearch\Xunsearch
     */
    public function setSort($field, bool $asc = false, bool $relevance = false)
    {
        $this->sort = [$field, $asc, $relevance];
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     *
     * @author zxf
     * @date   2020年12月17日
     * @param string $field
     * @param integer $from
     * @param integer $to
     * @return static
     */
    public function setRange(string $field, int $from = null, int $to = null)
    {
        if ($field) {
            $this->range = [
                [$field, $from, $to]
            ];
        } else {
            $this->range = null;
        }
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @return string|array
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     *
     * @author zxf
     * @date   2020年12月17日
     * @param string $field
     * @param integer $from
     * @param integer $to
     * @return static
     */
    public function addRange(string $field, int $from = null, int $to = null)
    {
        if ($field) {
            if (is_null($this->range)) {
                $this->range = [
                    [$field, $from, $to]
                ];
            } else {
                array_push($this->range, [$field, $from, $to]);
            }
        }
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @param string $field
     * @param string $term
     * @param integer $weight
     * @return static
     */
    public function addWeight(string $field, string $term, int $weight = 1)
    {
        if ($field && $term && $weight > 0) {
            if (is_null($this->weight)) {
                $this->weight = [
                    [$field, $term, $weight]
                ];
            } else {
                array_push($this->weight, [$field, $term, $weight]);
            }
        }
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @param string $field
     * @param string $term
     * @param integer $weight
     * @return static
     */
    public function setWeight(string $field, string $term, int $weight = 1)
    {
        if ($field && $term && $weight > 0) {
            $this->weight = [
                [$field, $term, $weight]
            ];
        } else {
            $this->weight = null;
        }
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @return array
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return boolean
     */
    public function getFuzzy()
    {
        return $this->fuzzy;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param integer $perPage
     * @param integer $page
     * @return static
     */
    public function setLimit(int $limit = 10)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param integer $offset
     * @return static
     */
    public function setOffset(int $offset = 0)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param integer $page
     * @return static
     */
    public function setPage(int $page = 1)
    {
        $this->offset = ($page - 1) * $this->limit;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月4日
     * @return integer
     */
    public function getPage()
    {
        $page = ($this->getOffset() / $this->getLimit()) + 1;
        return $page > 1 ? $page : 1;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $db
     * @return static
     */
    public function setDatabase(string $db)
    {
        $this->database = $db;
        $this->setSearch();
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     *
     * @author zxf
     * @date   2021年11月3日
     * @return integer
     */
    public function getDbTotal()
    {
        return $this->getSearch()->getDbTotal();
    }

    /**
     *
     * @author zxf
     * @date    2020年9月3日
     * @param  mixed  $method
     * @param  mixed $parameters
     * @throws XunsearchException
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getSearch(), $method)) {
            return $this->getSearch()->{$method}(...$parameters);
        } elseif (method_exists($this->getIndex(), $method)) {
            return $this->getIndex()->{$method}(...$parameters);
        } else {
            throw new XunsearchException('方法｛' . $method . '｝不存在！');
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $config
     * @return static
     */
    private function setSearch()
    {
        try {
            if (isset($this->config['databases'][$this->database])) {
                $this->search = new \XS($this->coverToIni($this->config['databases'][$this->database]));
                return $this;
            }
            throw new XunsearchException('配置错误！');
        } catch (XunsearchException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new XunsearchException('搜索服务设置失败！');
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return \XSSearch
     */
    private function getSearch()
    {
        return $this->search->getSearch();
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @return \XSIndex
     */
    private function getIndex()
    {
        return $this->search->getIndex();
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param array $config
     * @throws XunsearchException
     * @return string
     */
    private function coverToIni(array $config)
    {
        try {
            $string = '';
            foreach ($config as $key => $val) {
                if (is_array($val)) {
                    $string .= '[' . $key . "]\n";
                    foreach ($val as $k => $v) {
                        $string .= $k . ' = ' . $v . "\n";
                    }
                } else {
                    $string .= $key .' = '. $val . "\n";
                }
            }
            return $string;
        } catch (\Exception $e) {
            throw new XunsearchException('配置错误，请参考模板修改！');
        }
    }
}
