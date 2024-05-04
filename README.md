# LogC 日志中心
这是一个使用MySQL存储数据的日志中心服务。
基于webman进行开发，可以使用HTTP或WebSocket两种方式将日志发送到日志中心。
并提供一个简易的UI进行日志搜索。

## 起源
由于多项目日志查看非常不方便的原因，打算使用一个日志中心的服务，但是因为ELK太重了，而且大部分时间只是使用查询的功能，所以就诞生了这个LogC日志中心；

## 安装
```bash
git clone https://github.com/jarviscdr/logc.git

composer install

php webman start
```

## 使用
提供了一个配套的工具库，可前往[logc-client](https://github.com/jarviscdr/logc-client)查看
