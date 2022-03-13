<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} (author=\d+) [NC]
    RewriteRule .* - [F]
</IfModule>