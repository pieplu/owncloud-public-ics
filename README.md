# Owncloud public ics

This script create ics files from all calendar of a owncloud user.
This is a missing feature of owncloud calendar app.


## Installation

```sh
git clone https://github.com/pieplu/owncloud-public-ics.git nameYouWant
```

Add a .htaccess file (if you are under apache server)

```.htaccess
<IfModule mod_headers.c>
  # Add cache control for ICS files
  <FilesMatch "\.(ics)$">
    Header set Cache-Control "max-age=7200, public"
  </FilesMatch>
</IfModule>
<IfModule mod_rewrite.c>
  RewriteEngine on
  RewriteRule ^config.* - [R=404,L]
</IfModule>
#### DO NOT CHANGE ANYTHING ABOVE THIS LINE ####

ErrorDocument 403 //core/templates/403.php
ErrorDocument 404 //core/templates/404.php
```

Create a config.php and edit it

```sh
cp config.php.sample config.php
```

## Usage

At each access of `cloud.yourdomains.com/nameYouWant`, ics files will be created on the folder `nameYouWant`

Tips: Use cron to generate this every day/hour.

## Thanks

Thanks at lukas[at]statuscode.ch for his [script].


[script]:(https://statuscode.ch/2015/06/Combining-ownCloud-and-Google-calendar-for-public-room-availability/)
