<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelXunsearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @author zxf
 * @date   2020年9月3日
 * @method static \XSDocument search(string $query, bool $saveHighlight = true)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setDatabase(string $db)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getDatabase()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setFuzzy(bool $fuzzy = true)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getFuzzy()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setLimit(int $limit = 10)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getLimit()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setPage(int $page = 1)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setOffset(int $offset = 0)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getOffset()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch addIndex(array $items)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch delIndex(array $idItems)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch cleanIndex()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch beginRebuild()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch endRebuild()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch openBuffer(int $size = 4)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch closeBuffer()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getHotQuery(int $limit = 6, string $type = 'total')
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getRelatedQuery(string $query = null, int $limit = 6)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setMultiSort(array $fields, bool $reverse = false, bool $relevance = false)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setFacets($field, bool $exact = false)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setSort($field, bool $asc = false, bool $relevance = false)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setRange(string $field, int $from = null, int $to = null)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch addRange(string $field, int $from = null, int $to = null)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setAutoSynonyms(bool $synonyms = true)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch getLastCount()
 * @method static \Seffeng\LaravelXunsearch\Xunsearch count(string $query = null)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch terms(string $query = null, bool $convert = true)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch setWeight(string $field, string $term, int $weight = 1)
 * @method static \Seffeng\LaravelXunsearch\Xunsearch addWeight(string $field, string $term, int $weight = 1)
 *
 * @see \Seffeng\LaravelXunsearch\Xunsearch
 */
class Xunsearch extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'seffeng.laravel.xunsearch';
    }
}
