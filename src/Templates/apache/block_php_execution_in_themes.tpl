<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^wp\-content/themes/.*\.(?:php[1-7]?|pht|phtml?|phps)\.?$ - [NC,F]
</IfModule>