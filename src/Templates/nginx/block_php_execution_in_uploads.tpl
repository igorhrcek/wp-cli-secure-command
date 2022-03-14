location ~ ^/wp\-content/uploads/.*\.(?:php[1-7]?|pht|phtml?|phps)$ {
deny all;
}