# wp-cli/secure-command
Official website: [Hackthewp.com](https://hackthewp.com/)
Manages common security aspects of WordPress. Supports nginx and Apache.

## Basic Usage
This package implements the following commands:

### Deploy All Security rules

Deploys all above-mentioned rules at once.

```bash
wp secure all
```

### Remove All Security Rules

Removes all security rules.

```bash
wp secure flush
```

### Block the access to sensitive files and directories
```bash
wp secure block-access <what-to-block>
```

By default, this command blocks the direct access to sensitive files and directories:
`readme.txt`, `readme.html`, `xmlrpc.php`, `wp-config.php`, `wp-admin/install.php`, `wp-admin/upgrade.php`, `.git`, `svn`, `cache` and `vendors`

Possible options are:
- sensitive-files
- sensitive-directories
- xmlrpc
- htaccess
- custom
- all (does all the above)

Examples:

```bash
wp secure block-access sensitive-files
wp secure block-access sensitive-directories
wp secure block-access xmlrpc
wp secure block-access htaccess
wp secure block-access all
```

However, you can also block custom files and/or folders of your choice. To do that you should use `custom` argument
and pass one of two additional options `--files` and/or `--directories`.

If you want to block custom files, make sure that you pass only file names, not a full file paths. 

Examples:

````bash
wp secure block-access custom --files=dump.sql,phpinfo.php,adminer.php
wp secure block-access custom --directories=wp-content/mu-plugins
````

### Block Author Scanning

```bash
wp secure block-author-scanning
```

Blocks author scanning. Author scanning is a common technique of brute force attacks on WordPress. It is used to crack passwords for the known usernames and to gather additional information about the WordPress itself.

Examples:

```bash
wp secure block-author-scanning
```

### Block Direct Access and Execution in certain directories

```bash
wp secure block-php-execution <where>
```

Blocks direct access and execution of PHP files in `wp-content/plugins`, `wp-content/uploads`, `wp-content/themes` and `wp-includes` directories.

You need to specify where you want to prevent direct access to PHP files. Possible options are:
- all
- plugins
- uploads
- themes
- wp-includes

Examples:

```bash
wp secure block-php-execution all
wp secure block-php-execution plugins
wp secure block-php-execution uploads
wp secure block-php-execution themes
wp secure block-php-execution wp-includes
```

### Disable Directory Browsing
```bash
wp secure disable-directory-browsing
```

Disables directory browsing.

By default, when your web server does not find an index file (i.e. a file like index.php or index.html), it
automatically displays an index page showing the contents of the directory.
This could make your site vulnerable to hack attacks by revealing important information needed to exploit a vulnerability in a WordPress plugin, theme, or your server in general.

Examples:

```bash
wp secure disable-directory-browsing
```

### Disable WordPress File Editor

Disables the WordPress file editor. It could be used to edit arbitrary files using the web interface.
This makes it easier for attackers to change files on the server using a web browser.

```bash
wp secure disable-file-editor
```

### Fix file and directory permissions

```bash
wp secure fix-permissions
```

Use this command to verify that the permissions of all files and directories are set according the WordPress recommendations.
This command will set **0666** to all files and **0755** to all folders inside WordPress installation.

**IMPORTANT: Don't use this command if you don't know what you are doing here!**

### Check the integrity of WordPress files

Downloads MD5 checksums for the current version from WordPress.org, and compares those checksums against the currently
installed files.

It also returns a list of files that shouldn't be part of default WordPress installation, which can be very useful when you are
looking for a possible injected files.

Examples:

```bash
wp secure integrity-scan
```

## Global options

### Remove single security rule
Using `--remove` with any rule command, you can remove it from configuration.

```bash
wp secure block-access xmlrpc --remove
```

### Get the output instead of writing in configuration files
Using `--output` option with any rule command, you can see actual rule code which you can inspect or manually copy to any file of your choice.

```bash
wp secure block-access htaccess --output
wp secure block-access htaccess --output --server=nginx
```

### Specify server type
By default, all rules are generated for Apache or LiteSpeed web servers that utilize `.htaccess` file. However, you can use `--server` to specify nginx if you want.

```bash
wp secure block-access htaccess --server=nginx
```

### Specify custom file path
By default, all commands assume that rules should be written in the root of WordPress installation in `.htaccess` and `nginx.conf`, depending on which server you choose.
However, you can specify a custom file path that is going to be used for storing security rules.

```
wp secure block-access htaccess --file-path=/home/user/mysite.com/.htaccess
```

## Important Note for nginx users
The nginx rules are stored in the `nginx.conf` file. However, for rules to actually work, you need to manually include this file in your vhost configuration and then restart nginx server:
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
