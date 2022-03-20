# wp-cli/secure-command
Manages common security aspects of WordPress. Supports nginx and Apache.

## Basic Usage
This package implements the following commands:

**wp secure block_access_to_htaccess**

Blocks access to `.htaccess` and `nginx.conf` files.

```
wp secure block_access_to_htaccess [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

**wp secure block_access_to_sensitive_directories**

Blocks direct access to sensitive directories - `.git`, `svn`, `cache` and `vendors`

```
wp secure block_access_to_sensitive_directories [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

**wp secure block_access_to_sensitive_files**

Blocks direct access to sensitive files - `readme.txt`, `readme.html`, `wp-config.php`, `wp-admin/install.php` and `wp-admin/upgrade.php`

```
wp secure block_access_to_sensitive_files [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

**wp secure block_access_to_xmlrpc**

Blocks direct access XML-RPC

```
wp secure block_access_to_xmlrpc [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure block_author_scanning

Blocks author scanning. Author scanning is a common technique of brute force attacks on WordPress. It is used to crack passwords for the known usernames and to gather additional information about the WordPress itself.

```
wp secure block_author_scanning [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure block_php_execution_in_plugins

Blocks direct access and execution of PHP files in `wp-content/plugins` directory.

```
wp secure block_php_execution_in_plugins [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure block_php_execution_in_uploads

Blocks direct access and execution of PHP files in `wp-content/uploads` directory.

```
wp secure block_php_execution_in_uploads [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure block_php_execution_in_themes

Blocks direct access and execution of PHP files in `wp-content/themes` directory.

```
wp secure block_php_execution_in_themes [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure block_php_execution_in_wp_includes
Blocks direct access and execution of PHP files in include directories - `wp-admin/includes`, `wp-includes/*.php`, `wp-includes/js/tinymce/langs/*.php`, `wp-includes/theme-compat`

```
wp secure block_php_execution_in_wp_includes [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure disable_directory_browsing

Disables directory browsing.

By default when your web server does not find an index file (i.e. a file like index.php or index.html), it
automatically displays an index page showing the contents of the directory.
This could make your site vulnerable to hack attacks by revealing important information needed to exploit a vulnerability in a WordPress plugin, theme, or your server in general.

```
wp secure disable_directory_browsing [--remove] [--file-path=/alternative/path] [--output] [--server=apache|nginx]
```

### wp secure flush

Removes all security rules.

```
wp secure flush
```

### wp secure disable_file_editor

Disables the Wordpress file editor. It could be used to edit arbitrary files using the web interface.
This makes it easier for attackers to change files on the server using a web browser.
We suggest to disable the file editor off.

```
wp secure disable_file_editor
```

### wp secure enable_file_editor

Enables the Wordpress file editor. See wp secure disable-file-editor.

```
wp secure enable-file-editor
```

## Global options

### Remove single security rule
Using `--remove` with any rule command, you can remove it from configuration.

```
wp secure block_php_execution_in_wp_includes --remove
```

### Get the output instead of writing in configuration files
Using `--output` option with any rule command, you can see actual rule code which you can inspect or manually copy to any file of your choice.

```
wp secure block_php_execution_in_wp_includes --output
wp secure block_php_execution_in_wp_includes --output --server=nginx
```

### Specify server type
By default, all rules are generated for Apache or LiteSpeed web servers that utilize `.htaccess` file. However, you can use `--server` to specify nginx if you want.

```
wp secure block_php_execution_in_wp_includes --server=nginx
wp secure block_php_execution_in_wp_includes --server=--file-path=/home/user/mysite.com/nginx.conf
```

### Specify custom file path
By default, all commands assume that rules should be written in the root of WordPress installation in `.htaccess` and `nginx.conf`, depending on which server you choose.
However, you can specify a custom file path that is going to be used for storing security rules.

```
wp secure block_php_execution_in_plugins --file-path=/home/user/mysite.com/.htaccess
```

## Important Note for nginx users
nginx rules are stored in the `nginx.conf` file. However, for rules to actually work, you need to manually include this file in your vhost configuration and then restart nginx server:
```
systemctl restart nginx
```

WIth each rule deploy or removal, you also need to restart nginx server.

## Installing
To install the latest version of this package over what's included in WP-CLI, run:

```
wp package install git@github.com:igorhrcek/wp-cli-secure-command.git
```

## Development and testing
You need to set up two working WordPress installations on Apache and nginx. Usage of Docker containers is highly recommended - you can use the official WordPress Docker containers, BitNami or bootstrap your environment using [ddev](https://ddev.readthedocs.io/en/stable/users/cli-usage/#wordpress-quickstart). 

For testing you need to create `.env` file with the following content:
```
WORDPRESS_NGINX_PATH=wp/nginx
WORDPRESS_NGINX_URL=https://wpnginx.ddev.site
WORDPRESS_APACHE_PATH=wp/apache
WORDPRESS_APACHE_URL=https://wpapache.ddev.site
```

These paths and URLs are going to be used during tests, so make sure that they are accessible.

## Contributing
We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

