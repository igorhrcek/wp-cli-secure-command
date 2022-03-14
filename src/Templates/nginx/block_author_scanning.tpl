location / {
    if ($query_string ~ "author=\d+"){
        return 403;
    }
}