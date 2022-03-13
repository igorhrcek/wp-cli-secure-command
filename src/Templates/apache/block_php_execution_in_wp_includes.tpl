<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^wp-admin/includes/ - [F]
    RewriteRule !^wp-includes/ - [S=3]
    RewriteRule ^wp-includes/[^/]+\.php$ - [F]
    RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F]
    RewriteRule ^wp-includes/theme-compat/ - [F]
</IfModule>