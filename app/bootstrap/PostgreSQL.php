<?php

namespace app\bootstrap;

use Exception;
use Illuminate\Database\Events\QueryExecuted;
use support\Db;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Webman\Bootstrap;

class PostgreSQL implements Bootstrap
{
    public static function start($worker)
    {
        if($worker->id != 1) {
            return;
        }

        $extname = 'pg_jieba';

        $extensionResult = Db::table('pg_extension')->where('extname', $extname)->first();
        if(!empty($extensionResult)) {
            return;
        }

        try{
            Db::statement('create extension pg_jieba');
        }catch(Exception $th) {
            VD($th->getMessage());
        }

        // 使用分词扩展
        // select * from to_tsvector('jiebacfg', '分词文本');
    }

}
