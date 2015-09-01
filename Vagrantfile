# -*- mode: ruby -*-
# vi: set ft=ruby :


Vagrant.configure(2) do |config|

    config.vm.box = "ubuntu/trusty64"

    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

    # Enable provisioning with a shell script. Additional provisioners such as
    # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
    # documentation for more information about their specific syntax and use.

    config.vm.provision "shell", inline: <<-SHELL

        apt-get update
        apt-get install -y python-software-properties
        add-apt-repository -y ppa:nginx/stable
        apt-get update
        apt-get install -y nginx -o Dpkg::Options::="--force-confold"
        apt-get install -y php5-fpm php5-mysql php5-mcrypt php5-json php5-curl php5-memcached php5-memcache php5-gd

        export DEBIAN_FRONTEND=noninteractive
        debconf-set-selections <<< 'mysql-server mysql-server/root_password password pass'
        debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password pass'
        apt-get -y install mysql-server

        debconf-set-selections <<< 'phpmyadmin phpmyadmin/dbconfig-install boolean true'
        debconf-set-selections <<< 'phpmyadmin phpmyadmin/app-password-confirm password pass'
        debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-pass password pass'
        debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/app-pass password pass'
        debconf-set-selections <<< 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect '
        apt-get -q -y install phpmyadmin

        php5enmod mcrypt
        php5enmod json

        service php5-fpm restart

        if [ ! -d /home/vagrant/scripts ]
        then
            mkdir /home/vagrant/scripts
        fi

        if [ ! -d /etc/nginx/ssl ]
        then
            mkdir /etc/nginx/ssl
        fi

        if [ ! -f /etc/nginx/ssl/nginx.cert ]
        then
            openssl req -new -newkey rsa:2048 -days 999 -nodes -x509 -subj "/C=US/ST=Washington/L=Vancouver/O=Admin/CN=server.com" -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.cert
        fi

        echo "<?php

/* Servers configuration */
\\$i = 1;
\\$cfg['Servers'][\\$i]['verbose'] = '';
\\$cfg['Servers'][\\$i]['host'] = '127.0.0.1';
\\$cfg['Servers'][\\$i]['port'] = '';
\\$cfg['Servers'][\\$i]['socket'] = '';
\\$cfg['Servers'][\\$i]['connect_type'] = 'tcp';
\\$cfg['Servers'][\\$i]['extension'] = 'mysqli';
\\$cfg['Servers'][\\$i]['auth_type'] = 'config';
\\$cfg['Servers'][\\$i]['user'] = 'root';
\\$cfg['Servers'][\\$i]['password'] = 'pass';
\\$cfg['Servers'][\\$i]['hide_db'] = '(information_schema|performance_schema|mysql|phpmyadmin)';

\\$i++;
\\$cfg['Servers'][\\$i]['host'] = 'grav-dev.com';
\\$cfg['Servers'][\\$i]['port'] = '53306';
\\$cfg['Servers'][\\$i]['connect_type'] = 'tcp';
\\$cfg['Servers'][\\$i]['extension'] = 'mysqli';
\\$cfg['Servers'][\\$i]['auth_type'] = 'config';
\\$cfg['Servers'][\\$i]['user'] = 'gravitate';
\\$cfg['Servers'][\\$i]['password'] = 'qsZ\\$t<e2:a@nVtF2PH';

\\$cfg['DefaultLang'] = 'en-utf-8';
\\$cfg['ForceSSL'] = true;

if(is_dir('/vagrant/phpmyadmin_dbs'))
{
    foreach(glob('/vagrant/phpmyadmin_dbs/*.php') as \\$db_file)
    {
        \\$phpmyadmin_db = array();
        include(\\$db_file);
        if(!empty(\\$phpmyadmin_db['host']) && !empty(\\$phpmyadmin_db['user']) && !empty(\\$phpmyadmin_db['pass']))
        {
                \\$i++;
                \\$cfg['Servers'][\\$i]['verbose'] = (!empty(\\$phpmyadmin_db['label']) ? \\$phpmyadmin_db['label'] : '');
                \\$cfg['Servers'][\\$i]['host'] = \\$phpmyadmin_db['host'];
                \\$cfg['Servers'][\\$i]['port'] = (!empty(\\$phpmyadmin_db['port']) ? \\$phpmyadmin_db['port'] : '3306');
                \\$cfg['Servers'][\\$i]['connect_type'] = 'tcp';
                \\$cfg['Servers'][\\$i]['extension'] = 'mysqli';
                \\$cfg['Servers'][\\$i]['auth_type'] = 'config';
                \\$cfg['Servers'][\\$i]['user'] = \\$phpmyadmin_db['user'];
                \\$cfg['Servers'][\\$i]['password'] = \\$phpmyadmin_db['pass'];
                \\$cfg['Servers'][\\$i]['hide_db'] = '(information_schema|performance_schema|mysql|phpmyadmin)';
            }
    }
}" > /etc/phpmyadmin/conf.d/localhost.php

        echo 'server {
    listen 80 default_server;
    listen 443 ssl default_server;
    server_name localhost;

    ssl_certificate /etc/nginx/ssl/nginx.cert;
    ssl_certificate_key /etc/nginx/ssl/nginx.key;

    location / {
       root /vagrant/www;
       try_files $uri $uri/ =404;
       index index.php index.html home.html;
    }

    location /phpmyadmin {
        root /usr/share/;
        try_files $uri $uri/ index.php =404;
        index index.php index.html index.htm;
        location ~ ^/phpmyadmin/(.+\\.php)$ {
                try_files $uri =404;
                root /usr/share/;
                fastcgi_split_path_info ^(.+\\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $request_filename;
                include fastcgi_params;
                fastcgi_param PATH_INFO $fastcgi_script_name;
        }

        location ~* ^/phpmyadmin/(.+\\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
                root /usr/share/;
        }
    }

    location ~ [^/]\\.php(/|$) {
        root /vagrant/www;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param HTTPS $https;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Prevent nginx from serving dotfiles (.htaccess, .svn, .git, etc.)
    location ~ /\\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Prevent Malicious File Access
    location ~* \\.(sh|py|pl|rb|jar)$ {
        deny all;
    }
}' > /etc/nginx/sites-available/default

        echo '#!/usr/bin/env bash

        for file in /vagrant/www/* ; do

            FILE=$(basename $file)

            BASE_NAME=$FILE

            if [[ $BASE_NAME == *"local."* ]]
            then
                echo "server {
    listen 80;
    listen 443 ssl;
    server_name  $BASE_NAME;

    ssl_certificate /etc/nginx/ssl/nginx.cert;
    ssl_certificate_key /etc/nginx/ssl/nginx.key;

    root /vagrant/www/$BASE_NAME/public_html;

    location / {
        try_files \\$uri \\$uri/ /index.php?q=\\$uri&\\$args;
    }

    # WordPress Hardening from https://codex.wordpress.org/Hardening_WordPress
    location ~* wp-admin/includes { deny all; }
    location ~* wp-includes/theme-compat/ { deny all; }
    location ~* wp-includes/js/tinymce/langs/.*\\.php { deny all; }
    location ~* wp-includes/[^/]+\\.php\$ { deny all; }
    location ~* wp-content/uploads/.*\\.php\$ { deny all; }
    location ~* wp-content/cache/.*\\.php\$ { deny all; }
    location ~* wp-content/[^/]+\\.php\$ { deny all; }
    location /wp-content/ { internal; }
    location /wp-includes/ { internal; }

    location ~* \\.(svg|jpg|jpeg|gif|png|ico|css|zip|tgz|gz|bz2|odt|txt|tar|bmp|rtf|js|pdf|swf|avi|mp4|mp3|ogg|flv|htc|pscss|eot|woff|ttf)\$ {
        try_files \\$uri /404;
        expires 30d; #adjust to your static contents update frequency
    }

    location ~ [^/]\\.php(/|\$) {
        try_files \\$uri /404;
        fastcgi_split_path_info ^(.+\\.php)(/.+)\$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param "HTTPS" "\\$https";
        fastcgi_param SCRIPT_FILENAME \\$document_root\\$fastcgi_script_name;
        include fastcgi_params;
    }

    include /vagrant/www/$BASE_NAME/config/nginx/*.conf;

    # Prevent nginx from serving dotfiles (.htaccess, .svn, .git, etc.)
    location ~ /\\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Prevent Malicious File Access
    location ~* \\.(sh|py|pl|rb|jar)\$ {
        deny all;
    }

    location /nginx_status {
        stub_status on;
        access_log off;
        allow 127.0.0.1;
        deny all;
    }

    location = /xmlrpc.php {
        deny all;
        access_log off; #to prevent from filling up the access log file
    }
}" > /etc/nginx/sites-available/$BASE_NAME

                if ! [ -L /etc/nginx/sites-enabled/$BASE_NAME ]; then
                  ln -s /etc/nginx/sites-available/$BASE_NAME /etc/nginx/sites-enabled/
                fi

                echo "Created Site - $BASE_NAME"
            fi

        done

        service nginx reload

        ' > /home/vagrant/scripts/vagrant_setup.sh

    SHELL

    config.vm.network "forwarded_port", guest: 80, host: 8080
    config.vm.network "forwarded_port", guest: 443, host: 8443

    config.trigger.after [:provision, :up, :reload] do

        system('
            echo "
            rdr pass inet proto tcp from any to any port 80 -> 127.0.0.1 port 8080
            rdr pass inet proto tcp from any to any port 443 -> 127.0.0.1 port 8443
            " | sudo pfctl -ef - >/dev/null 2>&1;
            echo "Add Port Forwarding (80 => 8080)\nAdd Port Forwarding (443 => 8443)"
        ')

        run_remote "bash /home/vagrant/scripts/vagrant_setup.sh"

    end

    config.trigger.after [:halt, :destroy] do
        system("sudo pfctl -ef /etc/pf.conf > /dev/null 2>&1; echo '==> Removing Port Forwarding'")
    end

end
