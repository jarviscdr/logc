<?php

declare(strict_types=1);

use app\exception\BusinessException;
use support\Db;
use support\Response;

function VD(...$data): void {
    var_dump(...$data);
}

function VE($data, $return = false): void {
    var_export($data, $return);
}

/**
 * 成功响应
 *
 * @param array  $data
 * @param string $msg
 * @param int    $code
 * @param mixed  $headers
 * @param mixed  $cookies
 *
 * @author Jarvis
 * @date   2024-02-16 16:54
 */
function success($data = [], $msg = 'success', $code = 0, $headers = [], $cookies = []): Response {
    $response = json([
        'code' => $code,
        'msg'  => $msg,
        'data' => $data ?? new stdClass,
    ]);

    if (! empty($headers)) {
        $response->withHeaders($headers);
    }

    if (! empty($cookies)) {
        foreach ($cookies as $key => $value) {
            $response->cookie($key, $value, null, '/', '', false, true);
        }
    }

    return $response;
}

/**
 * 错误响应
 *
 * @param string $msg
 * @param int    $code
 * @param array  $data
 *
 * @author Jarvis
 * @date   2024-02-16 16:54
 */
function error($msg = 'error', $code = 1, $data = []): Response {
    return json([
        'code' => $code,
        'msg'  => $msg,
        'data' => $data ?? new stdClass,
    ]);
}

/**
 * 抛出业务异常
 *
 * @param string $msg
 * @param int    $code
 * @param array  $data
 *
 * @author Jarvis
 * @date   2024-02-16 16:55
 */
function BE($msg = '操作异常', $code = 1, $data = []): void {
    throw new BusinessException($msg, $code, $data);
}

/**
 * PostgreSQL 结巴分词
 *
 * @param  string                               $str
 * @param  mixed                                $field
 * @param  mixed                                $raw
 * @return Illuminate\Database\Query\Expression
 *
 * @author Jarvis
 * @date   2024-02-24 00:43
 */
function JiebaRaw($field, $str = '?', $raw = false) {
    if ($str != '?') {
        $str = "'{$str}'";
    }

    $sql = "\"{$field}\" @@ to_tsquery('jiebacfg', {$str})";

    if ($raw) {
        return Db::raw($sql);
    }

    return $sql;
}

/**
 * 数组转字符串
 *
 * @param mixed $array
 * @param mixed $separator
 * @param mixed $lineBreak
 *
 * @author Jarvis
 * @date   2024-02-24 00:43
 */
function arrayToString($array, $separator = ':', $lineBreak = ';') {
    $result = '';

    // 递归函数定义
    $arrayTraverser = function ($array, $prefix = '') use (&$arrayTraverser, $separator, $lineBreak, &$result): void {
        foreach ($array as $key => $value) {
            // 如果当前元素是数组，则递归处理
            if (is_array($value)) {
                $newPrefix = $prefix.$key.$separator;
                $arrayTraverser($value, $newPrefix);
            } else {
                // 否则，将键和值添加到结果字符串中
                $result .= $prefix.$key.$separator.$value.$lineBreak;
            }
        }
    };

    // 从根数组开始递归遍历
    $arrayTraverser($array);

    // 移除最后一个多余的行结束符
    $result = rtrim($result, $lineBreak);

    return $result;
}

/**
 * MySQL 全文搜索关键字
 *
 * @return string
 *
 * @author Jarvis
 * @date   2024-05-01 00:34
 */
function fulltextKeyword(string $keywords) {
    // 拆分关键字
    $keywords = explode(' ', $keywords);
    // 去除空白字符
    $keywords = array_filter($keywords);
    // 去除重复关键字
    $keywords = array_unique($keywords);

    // 处理关键字
    $keywords = array_map(function ($keyword) {
        if (strpos($keyword, '+') === 0 || strpos($keyword, '-') === 0) {
            return $keyword;
        }

        return "+\"{$keyword}\"";
    }, $keywords);

    return implode(' ', $keywords);
}

// 解析关键字
function parseFulltextKeyword(string $keywords): array {
    $keywords = explode(' ', trim($keywords));
    array_map(fn ($keyword) => trim('+- ', $keyword), $keywords);

    return $keywords;
}
