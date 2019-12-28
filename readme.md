
php >= 7.2.* Laravel 6.*

安装步骤
~~~
- cp .env.example .env (copy .env files)
- composer install
- php artisan key:generate
- php artisan jwt:auth
- php artisan migrate --seed
~~~

拓展工具
- 逆向数据填充
[iseed](https://github.com/orangehill/iseed)
~~~ 
单张表/多张表
php artisan iseed my_table
php artisan iseed my_table,another_table

指定类名前缀，防止与原seeder文件冲突：
php artisan iseed my_table --classnameprefix=Customized
~~~
