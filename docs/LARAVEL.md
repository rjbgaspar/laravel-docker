## Installing Laravel

Laravel utilizes Composer to manage its dependencies. So, before using Laravel, make sure you have Composer installed on your machine.
Via Laravel Installer

First, download the Laravel installer using Composer:


```bash
docker compose exec app composer global require laravel/installer
```
```bash
docker compose exec app composer create-project --prefer-dist laravel/laravel /var/www/tmp && mv -f /var/www/tmp/{.,}* /var/www && rm -rf /var/www/tmp/
```






sudo chown -R rjbgaspar /home/rjbgaspar/.composer



```bash
nano ~/.bash_profile 
export PATH=~/.composer/vendor/bin:$PATH
```

nano ~/.bash_profile docker compºose down
