# Vagrant-Local-Server



STEP 1  
Install Vagrant  
http://www.vagrantup.com/downloads.html  

STEP 2  
Install VirtualBox  
https://www.virtualbox.org/wiki/Downloads  

STEP 3  
Download and extract this repo package to the directory of your liking (recommended ~/vagrant)
https://github.com/GravitateDesignStudio/Vagrant-Local-Server/archive/master.zip  

STEP 4  
Use Terminal to "cd" into the extracted "local" folder. (Ex. ~/vagrant/local)  

STEP 5  
Start it up with  
```vagrant up``` OR ```vagrant up nginx``` for (nginx)  
```vagrant up apache``` for (apache)  
(Your fist start will take some time to download the Disk Image)  

That's it!

You should now be able to view your sites  
[http://localhost](http://localhost)  
OR  
[http://127.0.0.1](http://127.0.0.1)  

To get to PhpMyAdmin go here:  
[http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
\* Apache and Nginx use different Databases so the data will not be transferable between the two

## Management ##
Turning off  
To turn off use ```vagrant halt```  

Switching from Nginx to/from Apache  
```vagrant halt```  
```vagrant up apache```  
```vagrant halt```  
```vagrant up``` or ```vagrant up nginx```  

Adding a new site  
Just include another folder in the /local/www folder.  Make sure to name the folder the same as you want the domain to be and also include a "public_html" folder in it.  
Then run ```vagrant reload``` (nginx) OR ```vagrant reload apache```



