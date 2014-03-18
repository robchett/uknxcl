#!/usr/bin/env bash

echo 'Creating log'
sudo touch /tmp/install.log
sudo chmod 777 /tmp/install.log

echo 'Installing nano'
sudo yum install -y nano >> /tmp/install.log 2>>/tmp/install.log

echo 'Changed default editor to nano'
echo 'export EDITOR=nano' >> ~/.bashrc

echo 'Adding aliases'
su vagrant -c "echo 'alias yum=\"sudo yum\"' >> ~/.bashrc"
su vagrant -c "echo 'alias service=\"sudo service\"' >> ~/.bashrc"
su vagrant -c "echo 'alias la=\"ls -la\"' >> ~/.bashrc"
su vagrant -c ". ~/.bashrc"

echo 'Mananging git settings'
su vagrant -c "git config --global color.ui true"
su vagrant -c "git config --global user.email \"robchett@gmail.com\""
su vagrant -c "git config --global user.name \"Robert Chettleburgh\""

echo 'Installing tig'
yum install -y tig >> /tmp/install.log 2>>/tmp/install.log

echo 'Adding Remi Repos'
sudo rpm -Uhv http://rpms.famillecollet.com/enterprise/remi-release-6.rpm >> /tmp/install.log 2>>/tmp/install.log
sudo yum-config-manager --enable remi >> /tmp/install.log 2>>/tmp/install.log
sudo yum-config-manager --enable remi-php55 >> /tmp/install.log 2>>/tmp/install.log

echo 'Updating packages'
yum -y update >> /tmp/install.log 2>&1

echo 'Installing nginx/php/mysql/memcahced/redis'
yum -y install nginx php-fpm php-common mysql mysql-server memcached redis >> /tmp/install.log 2>>/tmp/install.log

echo 'Installing PHP Extensions'
yum --enablerepo=remi,remi-php55 install -y php-pecl-apc php-cli php-pear php-pdo php-mysqlnd php-pecl-memcached php-mbstring php-mcrypt php-xml php-devel >> /tmp/install.log 2>>/tmp/install.log

sudo pecl install xdebug > /dev/null 2>&1
echo "zend_extension=\"/usr/lib64/php/modules/xdebug.so\"" > ~/xdebug.ini
echo "[xdebug]" >> ~/xdebug.ini
echo "xdebug.idekey=\"PHPSTORM\"" >> ~/xdebug.ini
echo "xdebug.remote_enable=1" >> ~/xdebug.ini
echo "xdebug.remote_host=\"192.168.0.1"\" >> ~/xdebug.ini
echo "xdebug.remote_port=9089" >> ~/xdebug.ini
echo "xdebug.remote_handler=\"dbgp\"" >> ~/xdebug.ini
echo "xdebug.profiler_enable_trigger = 1" >> ~/xdebug.ini
echo "xdebug.profiler_output_dir = \"/tmp/xdebug/profiles\"" >> ~/xdebug.ini
sudo mv ~/xdebug.ini /etc/php.d/

sudo pecl install zendopcache-7.0.3 > /dev/null 2>&1
echo "[opcache]" > ~/opcache.ini
echo "zend_extension=opcache.so" >> ~/opcache.ini
echo "opcache.memory=1" >> ~/opcache.ini
echo "opcache.enable_cli=1" >> ~/opcache.ini
echo "opcache.memory_consumption=128" >> ~/opcache.ini
echo "opcache.interned_strings_buffer=8" >> ~/opcache.ini
echo "opcache.use_accelerated_files=4000" >> ~/opcache.ini
echo "opcache.max_wasted_percentage=5" >> ~/opcache.ini
echo "opcache.use_cwd=1" >> ~/opcache.ini
echo "opcache validate_timestamps=1" >> ~/opcache.ini
echo "opcache.fast_shutdown=1" >> ~/opcache.ini
sudo mv ~/opcache.ini /etc/php.d/

echo "[session]" > ~/redis.ini
echo "session.save_handler = redis" >> ~/redis.ini
echo "session.save_path = \"tcp://localhost:6379\"" >> ~/redis.ini
sudo mv ~/redis.ini /etc/php.d/

echo 'Starting Services'
service nginx start >> /tmp/install.log 2>>/tmp/install.log
service php-fpm start >> /tmp/install.log 2>>/tmp/install.log
service mysqld start >> /tmp/install.log 2>>/tmp/install.log
service memcached start >> /tmp/install.log 2>>/tmp/install.log

echo 'Setting up load order'
sudo chkconfig mysqld on >> /tmp/install.log 2>>/tmp/install.log
sudo chkconfig nginx on >> /tmp/install.log 2>>/tmp/install.log
sudo chkconfig php-fpm on >> /tmp/install.log 2>>/tmp/install.log
sudo chkconfig memcached on >> /tmp/install.log 2>>/tmp/install.log
sudo chkconfig redis on >> /tmp/install.log 2>>/tmp/install.log

echo 'Configuring MYSQL'
/usr/bin/mysqladmin -u root password 'xZ14G2649k'
mysql --user=root -pxZ14G2649k -e "CREATE USER 'admin'@'192.168.0.1' IDENTIFIED BY '';"
mysql --user=root -pxZ14G2649k -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'192.168.0.1';"
mysql --user=root -pxZ14G2649k -e "GRANT RELOAD,PROCESS ON *.* TO 'admin'@'192.168.0.1';"
mysql --user=root -pxZ14G2649k -e "FLUSH PRIVILEGES;"

echo 'Configuring PHP'
sudo mkdir /var/lib/php/session/
sudo chmod 777 /var/lib/php/session/