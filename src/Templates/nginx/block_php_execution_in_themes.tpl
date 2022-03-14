location ~ ^/wp\-content/themes/.*\.(?:php[1-7]?|pht|phtml?|phps)$ {
    deny all;
}