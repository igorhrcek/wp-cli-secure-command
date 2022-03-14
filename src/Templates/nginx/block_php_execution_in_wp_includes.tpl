location ~ ^/wp-includes/[^/]+\.php$ {
    deny all;
}

location ~ ^/wp-includes/js/tinymce/langs/.+\.php$ {
    deny all;
}

location ~ ^/wp-includes/theme-compat/ {
    deny all;
}

location ~ ^/wp-admin/includes/ {
    deny all;
}