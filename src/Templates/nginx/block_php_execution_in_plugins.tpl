location ~ ^/wp\-content/plugins/.*\.(?:php[1-7]?|pht|phtml?|phps)$ {
    deny all;
}