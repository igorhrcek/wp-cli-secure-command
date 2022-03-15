<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^wp\-content/uploads/.*\.(?:php[1-7]?|pht|phtml?|phps)\.?$ - [F]
</IfModule>