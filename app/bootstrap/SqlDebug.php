<?php

namespace app\bootstrap;

use Illuminate\Database\Events\QueryExecuted;
use support\Db;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Webman\Bootstrap;

class SqlDebug implements Bootstrap
{
    /**
     * 自定义输出格式，否则输出前面会带有当前文件，无用信息
     * @param $var
     * @return void
     */
    public static function dumpvar($var): void
    {
        $cloner = new VarCloner();
        $dumper = new CliDumper();
        $dumper->dump($cloner->cloneVar($var));
    }

    public static function start($worker)
    {
        // Is it console environment ?
        $is_console = !$worker;
        if ($is_console) {
            // If you do not want to execute this in console, just return.
            return;
        }
        if (!config("app.debug") || config("app.debug") === 'false') return;
        $appPath = app_path();

        Db::connection()->listen(function (QueryExecuted $queryExecuted) use ($appPath) {
            if (isset($queryExecuted->sql) and $queryExecuted->sql !== "select 1") {
                $bindings = $queryExecuted->bindings;
                $sql = array_reduce(
                    $bindings,
                    function ($sql, $binding) {
                        return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
                    },
                    $queryExecuted->sql
                );

                // self::dumpvar("[sql] [time:{$queryExecuted->time} ms] [{$sql}]"); // 这句话是打印所有的sql
                // 下面是只打印app目录下产生的sql语句
                $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                foreach ($traces as $trace) {
                    if (isset($trace['file']) && isset($trace["function"])) {
                        if (str_contains($trace['file'], $appPath)) {
                            $file = str_replace(base_path(), '', $trace['file']);
                            $str = "[file] {$file}:{$trace['line']} [function]:{$trace["function"]}";
                            self::dumpvar("[sql] [time:{$queryExecuted->time} ms] [{$sql}]");
                            self::dumpvar($str);
                        }
                    }
                }
            }
        });
    }

}
