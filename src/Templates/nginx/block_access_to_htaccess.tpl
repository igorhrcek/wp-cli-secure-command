location ~ /\.htaccess$ {
    deny all;
}

location ~ /nginx.conf {
    deny all;
}