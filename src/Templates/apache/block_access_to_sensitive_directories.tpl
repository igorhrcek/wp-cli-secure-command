<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule (^|.*/)\.(git|svn|vendors)/.* - [F]
</IfModule>