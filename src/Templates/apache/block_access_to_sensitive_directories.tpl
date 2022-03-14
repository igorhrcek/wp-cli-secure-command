<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule (^|.*/)\.(git|svn|vendors|cache)/.* - [F]
</IfModule>