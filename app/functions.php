<?php

use support\Response;
use app\exception\BusinessException;
use support\Db;

function VD(...$data) {
    var_dump(...$data);
}

function VE($data, $return = false) {
    var_export($data, $return);
}

/**
 * 成功响应
 *
 * @param  array  $data
 * @param  string $msg
 * @param  int    $code
 * @return Response
 * @author Jarvis
 * @date   2024-02-16 16:54
 */
function success($data = [], $msg = 'success', $code = 0): Response
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data ?? new stdClass
    ]);
}

/**
 * 错误响应
 *
 * @param  string $msg
 * @param  int    $code
 * @param  array  $data
 * @return Response
 * @author Jarvis
 * @date   2024-02-16 16:54
 */
function error($msg = 'error', $code = 1, $data = []): Response
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data ?? new stdClass
    ]);
}

/**
 * 抛出业务异常
 *
 * @param  string $msg
 * @param  int    $code
 * @param  array  $data
 * @return void
 * @author Jarvis
 * @date   2024-02-16 16:55
 */
function BE($msg = '操作异常', $code = 1, $data = [])
{
    throw new BusinessException($msg, $code, $data);
}

/**
 * PostgreSQL 结巴分词
 *
 * @param  string $str
 * @return Illuminate\Database\Query\Expression
 * @author Jarvis
 * @date   2024-02-24 00:43
 */
function JiebaRaw($field, $str = '?', $raw = false){
    if($str != '?') {
        $str = "'{$str}'";
    }

    $sql = "\"{$field}\" @@ to_tsquery('jiebacfg', {$str})";

    if($raw) {
        return Db::raw($sql);
    }

    return $sql;
}