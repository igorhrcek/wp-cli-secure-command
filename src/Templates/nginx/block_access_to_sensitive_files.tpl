location = /wp-admin/install.php {
    deny all;
}

location = /wp-admin/upgrade.php {
    deny all;
}

location ~ /readme\.html$ {
    deny all;
}

location ~ /readme\.txt$ {
    deny all;
}

location ~ /wp-config.php$ {
    deny all;
}