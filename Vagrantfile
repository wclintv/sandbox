ENV['VBOX_INSTALL_PATH'] = ENV['VBOX_MSI_INSTALL_PATH']
# -*- mode: ruby -*-
# vi: set ft=ruby :


# Requirements:
#	- Ubuntu 14.04, vagrant box: 'ubuntu/trusty64'
#	- Laravel 5.4, not required but designed with Laravel in mind.
#	
# Description: 
#	- This script creates an environment that mirrors a linux GoDaddy Shared Hosting Account (2017).
#
# Provisions:
#	- Linux Ubuntu 14.04
#	- Apache 2.4.7
#	- MySql 5.6.33
#	- PHP 5.6.31
#		
# Notes:
#	- The script maps the apache web root from /var/www/html to /vagrant/public (laravel standard)
#	- On the Host machine, edit /etc/hosts, add a line with the ip address and custom domain
#	  that matches the settings in your VagrantFile, for example:
#			192.168.33.10  example.dev  
#	- Apaches default SSL is enabled.  The browser will throw a warning indicating that the SSL
#	  certificate is unsigned.  You can ignore this warning, and proceed to the site.
#	- For windows users, you may need to place this line of code at the top of your VagrantFile:
#			ENV['VBOX_INSTALL_PATH'] = ENV['VBOX_MSI_INSTALL_PATH']
#
# Warnings:
#	- This script is for testing purposes only, and should not be used to deploy a 
# 	  production environment.
#

# MySql Variables (these should match variables in the .env file of your Laravel project)

GODADDY_SHARED_HOSTING_LAMP_PROVISION = <<SCRIPT
	DB_DATABASE=snapdsk
	DB_USERNAME=snapdsk
	DB_PASSWORD=snapdsk

	sudo apt-get -y -qq update

	echo "############################"
	echo "#  INSTALLATION COMPLETE   #"echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo apt-get -y -qq update && sudo apt-get -y -qq upgrade

	echo "############################"
	echo "#    INSTALLING APACHE2    #"
	echo "############################"
	sudo apt-get -y install apache2
	echo "ServerName localhost" | sudo tee -a /etc/apache2/apache2.conf
	sudo service apache2 restart

	echo "############################"
	echo "#     CONFIGURING SSL      #"
	echo "############################"
	sudo sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
	sudo a2enmod rewrite
	sudo a2enmod ssl
	sudo a2ensite default-ssl.conf
	sudo service apache2 restart

	echo "###########################"
	echo "# INSTALLING BASE MODULES #"
	echo "###########################"
	sudo apt-get -y -qq update
	sudo apt-get -y install  build-essential python-software-properties git
	sudo add-apt-repository -y ppa:ondrej/php

	echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo apt-get -y -qq update

	echo "############################"
	echo "#      INSTALLING PHP      #"
	echo "############################"
	sudo apt-get -y install php5.6
	sudo apt-get -y install php5.6-mbstring
	sudo apt-get -y install php5.6-mcrypt
	sudo apt-get -y install php5.6-xml
	sudo apt-get -y install php5.6-curl
	sudo apt-get -y install php5.6-mysql
	sudo apt-get -y install php5.6-zip 
	sudo apt-get -y install php5.6-oauth
	sudo service apache2 restart

	echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo apt-get -y -qq update

	echo "############################"
	echo "#     INSTALLING MYSQL     #"
	echo "############################"
	sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $DB_PASSWORD"
	sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DB_PASSWORD"
	sudo apt-get -y install mysql-server-5.6

	echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo apt-get -y -qq update

	echo "###########################"
	echo "# CREATING MYSQL DATABASE #"
	echo "###########################"
	mysql -uroot -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;"
	mysql -uroot -p"$DB_PASSWORD" -e "CREATE USER '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
	mysql -uroot -p"$DB_PASSWORD" -e "GRANT ALL PRIVILEGES ON $DB_DATABASE.* TO '$DB_USERNAME'@'localhost';"
	mysql -uroot -p"$DB_PASSWORD" -e "FLUSH PRIVILEGES;"

	echo "###########################"
	echo "#  INSTALLING PHPMYADMIN  #"
	echo "###########################"
	sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
	sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $DB_PASSWORD"
	sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $DB_PASSWORD"
	sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $DB_PASSWORD"
	sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
	sudo apt-get -y install phpmyadmin
	echo "Include /etc/phpmyadmin/apache.conf" | sudo tee -a /etc/apache2/apache2.conf
	sudo service apache2 restart

	echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo apt-get -y -qq update


	echo "############################"
	echo "# ENABLING ERROR REPORTING #"
	echo "############################"
	sudo sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/5.6/apache2/php.ini
	sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php/5.6/apache2/php.ini

	echo "############################"
	echo "#   INSTALLING COMPOSER    #"
	echo "############################"
	sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
	sudo apt-get -y -qq update

	echo "############################"
	echo "#     MAPPING WEB ROOT     #"
	echo "############################"
	sudo rm -rf /var/www/html
	sudo ln -fs /vagrant/public /var/www/html

	echo "############################"
	echo "#      UPDATING LINUX      #"
	echo "############################"
	sudo service apache2 restart
	echo "############################"


SCRIPT


Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://vagrantcloud.com/search.
  config.vm.box = "ubuntu/trusty64"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # NOTE: This will enable public access to the opened port
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine and only allow access
  # via 127.0.0.1 to disable public access
  # config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder ".", "/vagrant", type: "nfs"


  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
      vb.gui = false
      vb.memory = "2048"
      vb.cpus = 2
    # change the network card hardware for better performance
    #vb.customize ["modifyvm", :id, "--nictype1", "virtio" ]
    #vb.customize ["modifyvm", :id, "--nictype2", "virtio" ]

    # suggested fix for slow network performance
    # see https://github.com/mitchellh/vagrant/issues/1807
    #vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    #vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]



  end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  #config.vm.provision "shell", path: "vg_bash_godaddy_shared_hosting.sh"
  config.vm.provision "shell", inline: GODADDY_SHARED_HOSTING_LAMP_PROVISION, privileged: false
end
