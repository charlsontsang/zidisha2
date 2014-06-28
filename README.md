zidisha2
========

P2P lending across the international wealth divide https://www.zidisha.org


Running Locally
===============

#### Homestead

It is recommended to run Zidisha within the Homestead Vagrant box.

Follow these instructions to setup Homestead:

http://laravel.com/docs/homestead

Once the Homestead Vagrant box is up and running,
you'll want to go through the following instructions on that box.


#### Install Elastic Search

http://forgerecipes.com/recipes/9


#### Clone Zidisha

```bash
$ cd /home/vagrant
$ git clone https://github.com/Zidisha/zidisha2.git
$ cd zidisha2
```

Open `app/start/global.php` and comment out line 97
```php
// require app_path() . '/config/propel/config.php';
```


#### Use composer to install dependencies

```bash
$ composer install
```


#### Generate config files (use default values)

```bash
$ php artisan setup
```


#### Use bower to install front-end dependencies

```bash
$ bower install
```

#### Undo the comment on line 97 of `app/start/global.php`

#### Create the database with some fake data

```bash
$ php artisan fake new
```

### You're done!
The website should run at `127.0.0.1:8000` (or your vagrant private ip address)
