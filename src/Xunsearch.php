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
        return $this->getSearch()
                ->setLimit($this->getLimit(), $this->getOffset())
                ->setFuzzy($this->getFuzzy())
                ->search($query, $saveHighlight);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param int $limit
     * @param string $type
     * @return array
     */
    public function getHotQuery(int $limit = 6, string $type = 'total')
    {
        return $this->getSearch()->getHotQuery($limit, $type);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function getRelatedQuery(string $query = null, int $limit = 6)
    {
        return $this->getSearch()->getRelatedQuery($query, $limit);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function getExpandedQuery(string $query, int $limit = 10)
    {
        return $this->getSearch()->getExpandedQuery($query, $limit);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param array $fields
     * @param bool $reverse
     * @param bool $relevance
     * @return \XSSearch
     */
    public function setMultiSort(array $fields, bool $reverse = false, bool $relevance = false)
    {
        return $this->getSearch()->setMultiSort($fields, $reverse, $relevance);
    }

    /**
     *
     * @author zxf
     * @date    2020年9月3日
     * @param mixed $field
     * @param bool $asc
     * @param bool $relevance
     * @return \XSSearch
     */
    public function setSort($field, bool $asc = false, bool $relevance = false)
    {
        return $this->getSearch()->setSort($field, $asc, $relevance);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $query
     * @return array
     */
    public function getCorrectedQuery(string $query = null)
    {
        return $this->getSearch()->getCorrectedQuery($query);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param mixed $field
     * @param bool $exact
     * @return \XSSearch
     */
    public function setFacets($field, bool $exact = false)
    {
        return $this->getSearch()->setFacets($field, $exact);
    }

    /**
     *
     * @author zxf
     * @date    2020年9月3日
     * @param  string $value
     * @param  bool $strtr
     * @return string
     */
    public function highlight(string $value, bool $strtr = false)
    {
        return $this->getSearch()->highlight($value, $strtr);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param string $field
     * @return array|mixed
     */
    public function getFacets(string $field)
    {
        return $this->getSearch()->getFacets($field);
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
                $doc->setFields($item);
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
     */
    public function beginRebuild()
    {
        $this->getIndex()->beginRebuild();
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     */
    public function endRebuild()
    {
        $this->getIndex()->endRebuild();
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param int $size
     */
    public function openBuffer(int $size = 4)
    {
        $this->getIndex()->openBuffer($size);
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     */
    public function closeBuffer()
    {
        $this->getIndex()->closeBuffer();
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
     * @param int $perPage
     * @param int $page
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
     * @return number
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param int $offset
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
     * @return number
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @author zxf
     * @date   2020年9月3日
     * @param int $page
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
