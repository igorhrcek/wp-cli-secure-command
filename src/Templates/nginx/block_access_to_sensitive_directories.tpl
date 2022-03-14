location ~ ^.*/\.git/.*$ {
    deny all;
}

location ~ ^.*/\.svn/.*$ {
    deny all;
}

location ~ ^.*/vendors/.*$ {
    deny all;
}

location ~ ^.*/cache/.*$ {
    deny all;
}