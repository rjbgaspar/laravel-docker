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

### Step by step


```bash 
docker compose exec app /bin/bash
```
```
composer create-project --prefer-dist laravel/laravel /var/www/tmp
mv -f /var/www/tmp/{.,}* /var/www
rm -rf /var/www/tmp/
```
Update db env

### Laravel not found 

```bash
nano ~/.bash_profile 
export PATH=~/.composer/vendor/bin:$PATH
```

composer create-project --prefer-dist laravel/laravel /var/www/tmp && mv -f /var/www/tmp/{.,}* /var/www && rm -rf /var/www/tmp/ 

# MySql
```bash
docker compose exec db /bin/bash
mysql -u sa -pSfpswd2023
use laravel-test;
```
