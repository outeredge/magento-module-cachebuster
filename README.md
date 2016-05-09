# magento-module-cachebuster
Adds md5 hash to assets for cachebusting post-deployment.

## mod_rewrite configuration

Add the following rewrite rule to your .htaccess file 

    <IfModule mod_rewrite.c>
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.+)\.([0-9a-f]{32})\.(.+)$ $1.$3 [L]
    </IfModule>

## nginx configuration

Add the following into your server block

    location ~ "(.+)\.([0-9a-f]{32})\.(.+)$" {
        expires max;
        try_files $uri $1.$3 =404;
    }
