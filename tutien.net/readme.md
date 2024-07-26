```bash
sudo apt-get install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php7.1 php7.1-cli php7.1-common php7.1-json php7.1-opcache php7.1-sqlite php7.1-curl php7.1-mbstring php7.1-mcrypt php7.1-zip php7.1-json curl php7.1-xml git unzip
ssh-keygen -o -t rsa -b 4096 -C "truyencv-server-02@yopmail.com"

curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

git clone git@gitlab.com:DoanhPHAM/truyencv-tool.git --branch=truyencv-server-2
cd truyencv-tool
composer install
cp .env.example .env

update .env

php artisan key:generate
sudo chmod -R 777 storage/logs/
sudo chmod -R 777 bootstrap/cache/

sudo crontab -e
* * * * * php /home/pham.van.doanh/truyencv-tool/artisan schedule:run >> /dev/null 2>&1
```
