<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule (^|.*/)\.({{directories}})/.* - [F]
</IfModule>