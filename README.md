# LogC 日志中心
这是一个使用MySQL存储数据的日志中心服务。
基于webman进行开发，可以使用HTTP将日志发送到日志中心。
并提供一个简易的UI进行日志搜索。


## 安装
```bash
git clone https://github.com/jarviscdr/logc.git

composer install

php webman start
```

##  Nginx配置
> 由于视图页面使用了text/event-stream事件流返回数据
>
> 所以Nginx需要配置为支持event-stream类型，可参考以下示例

```conf
# 负载均衡和重写
upstream logc {
    server 127.0.0.1:10001;
    keepalive 10240;
}

location ^~ /logc {
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header Host $http_host;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_http_version 1.1;
    proxy_set_header Connection "keep-alive";
    proxy_buffering off;
    proxy_cache off;
    proxy_read_timeout 24h;
    proxy_connect_timeout 24h;
    proxy_send_timeout 24h;
    rewrite ^/logc(/.*)$ $1 break;
    proxy_pass http://logc/;
}
```

## 使用
提供了一个配套的工具库，可前往[logc-client](https://github.com/jarviscdr/logc-client)查看
