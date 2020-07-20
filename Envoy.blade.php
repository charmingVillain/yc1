@servers(['test' => ['cykc@192.168.0.191'], 'ali_test' => ['root@47.106.10.106'], 'prod' => ['root@39.108.164.39']])

@task('deploy:test', ['on' => 'test'])
    cd /data/www/health_manage
    git pull origin develop
    yarn install
    composer install
    php artisan optimize
    php artisan queue:restart
    php artisan horizon:terminate
    yarn run prod
@endtask





