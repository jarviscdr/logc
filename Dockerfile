# 选择基础镜像
FROM jarviscdr/php:82

ENV TZ=Asia/Shanghai

# 设置工作目录
WORKDIR /projects/logc

COPY . /projects/logc/

RUN composer install

ENTRYPOINT ["sh", "-c"]
CMD ["php start.php start -d && php-fpm"]
