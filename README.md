# <center>Exchange backend Applictation Documentation</center>
#### Latest revision:
2022 March 18 by [Ali Khedmati](https://khedmati.ir)
## Technologies
* [PHP 8.1.3 LTS](https://php.net)
* [Laravel 9.5 LTS](https://laravel.com)
* [Redis 6.2 LTS](https://redis.io)
* [Laravel Horizon 5.9.2 LTS](https://laravel.com/docs/9.x/horizon)
* [Laravel Octane 1.2 LTS](https://laravel.com/docs/9.x/octane)
## Why we name it after exchange?
The Greek god <b>exchange</b> (the Roman Mercury ) was the god of translators and interpreters. He was the most clever of the Olympian gods, and served as messenger for all the other gods. He ruled over wealth, good fortune, commerce, fertility, and thievery. Because of his speed, he was sometimes considered a god of winds.
## System Requirements
* <b>Minimum system requirements</b>:

  * Ubuntu 16.04.
  * 2 GB of Ram.
  * 2 Core of CPU.
  * 100 MB of free HDD storage.

* <b> Recommended system requirements</b>:
  
  * Ubuntu 20.04.
  * 16 GB of Ram.
  * 16-24 Core of CPU.
  * 1 GB of free SSD / NVMe storage.

### Manual deploy

>This manual will guid you to build fresh instance of <b>exchange</b> from scratch.<br>You don't need to be an expert to deploy <b>exchange</b>! we have had put our best to make it all easy and understandable to all kind of users.<br>We assumed that you have fresh VPS with at least one <b>IPV4</b>, <b>Ubuntu 20.04 LTS</b>, SSH root access and at least 4GB of available RAM and minimum 2 core of CPUs.<br><b>Note:</b> <b>exchange</b> doesn't force you to use <b>Ubuntu</b> and you can feel free if you want to use other distros such as <b>Centos</b> or ...
### 0. DNS Management (Optional)
If you want to point your desired domain to your server, login to your domain DNS management panel and create an A record. the value of this A record has to be <code>IPV4</code> of your server.
The name of this A record, could be <code>@</code> if you want to point the main domain or it would be the name of your desired sub-domain.<br>However, you can skip this step if you want to access <b>exchange</b> via an <code>IPV4</code>.
Also, we strongly recommend you to use services like [Cloudflare](https://cloudflare.com) or [Arvan Cloud](https://arvancloud.com).
### 1. Login via ssh
```shell
ssh root@ip
```
### 2. Create new user
```shell
sudo adduser exchange
``` 
This will prompt for further information such as password and new user's name.

### 3. Grant <code>sudo</code> permission
```shell
sudo usermod -aG sudo exchange
```
### 4. Switch to recently created user
```shell
su exchange
```
### 5. Update the system and install required tools
```shell
sudo apt-get update 
sudo apt-get upgrade
sudo apt-get install git wget nano zip unzip htop curl
```
### 6. Install and configure MySql
* Install MySql via:
  ```shell
  sudo apt install mysql-server
  ```
* Start the interactive script by running:
  ```shell
  sudo mysql_secure_installation
  ```
* Connect to interactive MySql via:
  ```shell
  sudo mysql
  ```
* Create new User via:
  ```shell
  CREATE USER 'exchange'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
  ```
* Create new database via:
  ```shell
  CREATE DATABASE exchange;
  ```
* Grant all privileges to database via:
  ```shell
  GRANT ALL ON exchange.* TO 'exchange'@'localhost';
  ```
  > <b>Attention: Write down your password in somewhere safe. We still need to add this credential inside our laravel <code>.env</code> file in further steps.</b>

### 7. Install Redis
* Install redis-server:
    ```shell
    sudo apt-get install redis-server
    ```
* Open the Redis config file:
    ```shell
    sudo nano /etc/redis/redis.conf
    ```
  Inside this file:
    * find <code>supervised</code> directive which is set as <code>no</code> by default and change it to <code>systemd</code>.
    * locate <code>bind 127.0.0.1::1</code> and make sure it is uncommented (remove the # if it exists):
    * scroll to the SECURITY section and look for a commented directive that says <code># requirepass foobared</code>. Uncomment it and replace your strong password with <code>foobared</code>

* reload the redis service:
    ```shell
    sudo systemctl restart redis.service
    ```
* To check if redis had been installed and configured correctly, run this command:
    ```shell
    sudo systemctl status redis
    ```
* If Redis had been installed and started successfully, you should see something like this:
    ```shell
    ● redis-server.service - Advanced key-value store
    Loaded: loaded (/lib/systemd/system/redis-server.service; enabled; vendor >
    Active: active (running) since Thu 2021-09-09 21:53:32 +0430; 2min 30s ago
     Docs: http://redis.io/documentation,
           man:redis-server(1)
    Process: 24402 ExecStart=/usr/bin/redis-server /etc/redis/redis.conf (code=>
    Main PID: 24417 (redis-server)
    Tasks: 4 (limit: 2280)
    Memory: 1.8M
    CGroup: /system.slice/redis-server.service
           └─24417 /usr/bin/redis-server 127.0.0.1:6379
    
    Sep 09 21:53:31 exchange systemd[1]: Starting Advanced key-value store...
    Sep 09 21:53:32 exchange systemd[1]: redis-server.service: Can't open PID file /r>
    Sep 09 21:53:32 exchange systemd[1]: Started Advanced key-value store.
    ```
* Finally, to check if your authentication had been set successfully, login to your Redis-cli:
   ```shell
   redis-cli 
   ```
  Now, type this command:
   ```shell
   auth YOUR_REDIS_PASSWORD
   ```
### 8.Install PHP and its necessary modules
* Add Apt repository.
    ```shell
    sudo apt install software-properties-common
    sudo add-apt-repository ppa:ondrej/php
    sudo apt update
    ```
* Install PHP
    ```shell
    sudo apt-get install php8.1 php8.1-dev php8.1-swoole php8.1-redis php8.1-curl php8.1-zip php8.1-xml php8.1-bcmath php8.1-gmp php8.1-gd php8.1-mbstring
    ```
* After installing PHP, run the following command to make sure PHP had been successfully installed:
    ```shell
    php --version
    ```
* Open <code>php.ini</code>:
    ```shell
    sudo nano /etc/php/8.1/cli/php.ini
    ```
  Change <code>upload_max_filesize</code> and <code>post_max_size</code> like this:
    ```shell
    upload_max_filesize = 20M
    post_max_size = 20M
    ```
### 9. Install Composer
* Make sure you’re in your home directory, then retrieve the installer using curl:
    ```shell
    cd ~
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    ```
* Next, To install composer globally, use the following command which will download and install Composer as a system-wide command named composer, under <code>/usr/local/bin</code>:
    ```shell
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    ```
* After installing composer, run the following command to make sure composer had been successfully installed:
    ```shell
    composer --version
    ```
* Finally, remove <code>composer-setup.php</code>:
    ```shell
    rm -rf composer-setup.php
    ```
### 10. Clone and configure application
* Change your working directory:
     ```shell
     cd /var/www
     ```
* Clone application:
     ```shell
     sudo git clone https://git.vorna.dev/ali.khedmati/exchange-backend.git
     ```
* Manage permissions and ownerships:
     ```shell
     sudo chown -R $USER:www-data /var/www/exchange-backend/
     sudo usermod -aG www-data exchange
     ```
* Change your working directory to <code>exchange-backend</code>:
     ```shell
     cd /var/www/exchange-backend
     ```
* Copy <code>.env.example</code> to <code>.env</code>, open it and modify the attributes as you wish:
     ```shell
     cp .env.example .env
     nano .env
     ```
  >Attention: Do not use HTTPS yet! build your instance in HTTP protocol and after fetching SSL certs, comeback and change protocols to HTTPS.
* Install dependencies:
     ```shell
     composer install --optimize-autoloader --no-dev
     ```
* Set <code>APP_KEY</code>:
     ```shell
     php artisan key:generate
     ```
* Link storages:
     ```shell
     php artisan storage:link
     ```
* Run database migrations:
     ```shell
     php artisan migrate --seed
     ```
* Finally, optimize the application:
     ```shell
     php artisan optimize
     ```
### 11. Install and configure Nginx
* Install Nginx:
  ```shell
  sudo apt-get install nginx
  ```
* Open <code>nginx.conf</code>:
  ```shell
  sudo nano /etc/nginx/nginx.conf
  ```
    * set <code>user</code> to exchange.
    * set <code>worker_connection</code> to 2048.
    * Uncomment <code>multi_accept_on</code>

* Create new Nginx configuration file for exchange:
    ```shell
    sudo touch /etc/nginx/sites-available/exchange-backend.conf
    ```
* Copy and Paste the following snippet and change <code>yourdomain</code> as you wish:
    ```
    map $http_upgrade $connection_upgrade {
        default upgrade;
        ''      close;
    }
    
    server {
        listen 80;
        listen [::]:80;
        server_name domain.com;
        server_tokens off;
        root /var/www/exchange-backend/public;
    
        index index.php;
    
        charset utf-8;
    
        location /index.php {
            try_files /not_exists @octane;
        }
    
        location / {
            try_files $uri $uri/ @octane;
        }
     
        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }
    
        add_header Strict-Transport-Security "max-age=63072000; includeSubdomains;" always;
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
  
        access_log off;     
        error_page 404 /index.php;
    
        location @octane {
            set $suffix "";
     
            if ($uri = /index.php) {
                set $suffix ?$query_string;
            }
     
            proxy_http_version 1.1;
            proxy_set_header Host $http_host;
            proxy_set_header Scheme $scheme;
            proxy_set_header SERVER_PORT $server_port;
            proxy_set_header REMOTE_ADDR $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection $connection_upgrade;
     
            proxy_pass http://127.0.0.1:9581$suffix;
        }
    }
    ```
* Activate your configuration by linking to the config file from Nginx’s sites-enabled directory:
     ```shell
     sudo ln -s /etc/nginx/sites-available/exchange-backend.conf /etc/nginx/sites-enabled/
     ```
* Unlink the default configuration file from the <code>/sites-enabled/</code> directory:

     ```shell
     sudo unlink /etc/nginx/sites-enabled/default
     ```
* Reload and restart Nginx to apply the changes:

     ```shell
     sudo systemctl reload nginx
     sudo systemctl restart nginx
     ```

### 12. Install and configure SSL Certificates:
* Install Certbot:
    ```shell
    sudo apt-get install certbot python3-certbot-nginx
    ```
* Obtain new certificate:
    ```shell
    sudo certbot --nginx
    ```
* After finishing the wizard, you have to edit <code>.env</code> in <code>/var/www/exchange-backend</code> and update the location of your local certificates (Usually they're in <code>/etc/letsencrypt/live/</code>). Then, clear optimized files:
    ```shell
    php artisan optimize:clear
    ```
### 13. Install and configure Supervisor
* Install Supervisor:
    ```shell
    sudo apt-get install supervisor
    ```
* Create new configuration file:
    ```shell
    sudo nano /etc/supervisor/conf.d/exchange-backend.conf
    ```

- Put the following snippet in your <code>.conf</code> file:

    ```shell
    [program:exchange-octane]
    process_name=%(program_name)s_%(process_num)02d
    command=/usr/bin/php /var/www/exchange-backend/artisan octane:start --max-requests=1000 --workers=4 --task-workers=12 --port=9581
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    startsecs=0
    user=exchange
    redirect_stderr=false
  
    [program:exchange-horizon]
    process_name=%(program_name)s_%(process_num)02d
    command=/usr/bin/php /var/www/exchange-backend/artisan horizon
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    startsecs=0
    user=exchange
    redirect_stderr=false
   
    [program:exchange-websockets]
    process_name=%(program_name)s_%(process_num)02d
    command=/usr/bin/php /var/www/exchange-backend/artisan websockets:serve --port=6000
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    startsecs=0
    user=root
    redirect_stderr=false
   
    [program:exchange-orders-update]
    process_name=%(program_name)s_%(process_num)02d
    command=/usr/bin/php /var/www/exchange-backend/artisan orders:update
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    startsecs=0
    user=exchange
    redirect_stderr=false
    
    [program:exchange-markets-update]
    process_name=%(program_name)s_%(process_num)02d
    command=/usr/bin/php /var/www/exchange-backend/artisan markets:update
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    startsecs=0
    user=exchange
    redirect_stderr=false
    ```
  ><b>Attention:</b> We have to run <code>exchange-streams</code> program via root user because it needs read access to SSL certificates.

* Make Supervisor to re-read newly created <code>.conf</code> file:

    ```shell
    sudo supervisorctl reread
    ```

* Finally, update Supervisor:

    ```shell
    sudo supervisorctl update
    sudo supervisorctl restart all
    ```

* You can check Supervisor's status:

    ```shell
    sudo supervisorctl status
    ```
### 14. Install and configure Websocket stream server:
* Create new configuration file:
     ```shell
     sudo touch /etc/nginx/sites-available/exchange-stream.conf
     ```
* Copy and Paste the following snippet and change <code>yourdomain</code> as you wish + update the real address of ssl certs:

     ```shell
     upstream stream {
         server yourdomain:6000;
     }

     server {
         listen 6001 ssl http2;
         server_name yourdomain;
         location / {
             proxy_pass https://stream;
             proxy_http_version 1.1;
             proxy_set_header Upgrade $http_upgrade;
             proxy_set_header Connection 'upgrade';
             proxy_set_header Host $host;
             proxy_cache_bypass $http_upgrade;
             proxy_set_header X-Forwarded-For    $proxy_add_x_forwarded_for;
             proxy_set_header X-Forwarded-Proto  https;
             proxy_set_header X-VerifiedViaNginx yes;
             proxy_read_timeout                  60;
             proxy_send_timeout                  60;
             proxy_connect_timeout               60;
             send_timeout                        60;
             proxy_redirect                      off;
         }
         ssl_certificate /etc/letsencrypt/live/exchange-backend.vorna.dev/fullchain.pem;
         ssl_certificate_key /etc/letsencrypt/live/exchange-backend.vorna.dev/privkey.pem;
         include /etc/letsencrypt/options-ssl-nginx.conf;
         ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
     }
     ```
* Activate your configuration by linking to the config file from Nginx’s sites-enabled directory:
     ```shell
     sudo ln -s /etc/nginx/sites-available/exchange-stream.conf /etc/nginx/sites-enabled/
     ```
* Reload and restart Nginx to apply the changes:
     ```shell
     sudo systemctl reload nginx
     sudo systemctl restart nginx
     ```
### 15. Manage cronjob via crontab
exchange has 2 command to be place in system's crontab:
* <b>Laravel Cronjob</b>
* <b>Certbot SSL certificates renewal Cronjob</b>

To apply cronjobs:
* Open <code>crontab</code>:
     ```shell
     crontab -e
     ```
* Copy and paste the following snippet and save the file:

     ```shell
     0 12 * * * /usr/bin/certbot renew --quiet
     * * * * *  /usr/bin/php /var/www/exchange-backend/artisan schedule:run >> /dev/null 2>&1
     ```
