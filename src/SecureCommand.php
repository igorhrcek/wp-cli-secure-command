<?php

namespace WP_CLI_Secure;

use WP_CLI;
use WP_CLI\Process;
use WP_CLI\Utils;
use WP_CLI_Command;
use WP_CLI_Secure\SubCommands\BlockAccessToHtaccess;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveDirectories;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveFiles;
use WP_CLI_Secure\SubCommands\BlockAccessToXmlRpc;
use WP_CLI_Secure\SubCommands\BlockAuthorScanning;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInPlugins;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInThemes;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInUploads;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInWpIncludes;
use WP_CLI_Secure\SubCommands\DisableDirectoryBrowsing;

/**
 * Adds or removes security rules to .htaccess or nginx.conf to strengthen security of the WordPress installation
 *
 * Secure is capable of providing configurations for both Apache and nginx. Apache is a default, but you can manually
 * specify server type.
 *
 * By default, all rules are written in the .htaccess file or nginx.conf file if nginx is set as a server type.
 * You can also set a custom path to a file that should be used for writing security rules into.
 *
 * ## EXAMPLES
 *
 *     # Add security rule
 *     $ wp secure disable-directory-browsing
 *     Success: Directory Browsing is disabled.
 *
 *     # Remove security rule
 *     $ wp secure disable-directory-browsing --disable
 *     Success: Directory Browsing is enabled.
 *
 *     # Remove all security rules
 *     $ wp secure flush
 *
 *     # Add security rule to a custom file
 *     $ wp secure disable-directory-browsing --path=/some/file/path/.htaccess
 *
 *     # Display the output only (no writing)
 *     $ wp secure disable-directory-browsing --output
 *
 *     # Specify web server type
 *     $ wp secure disable-directory-browsing --server=nginx
 *
 *
 * @package wp-cli
 */
class SecureCommand extends WP_CLI_Command {
    /**
     * Disables directory browsing.
     *
     * By default when your web server does not find an index file (i.e. a file like index.php or index.html), it
     * automatically displays an index page showing the contents of the directory.
     * This could make your site vulnerable to hack attacks by revealing important information needed to exploit a vulnerability in a WordPress plugin, theme, or your server in general.
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure disable_directory_browsing
     *     Success: Directory Browsing security rule is now active.
     *
     */
    public function disable_directory_browsing($args, $assoc_args) : void {
        (new DisableDirectoryBrowsing($assoc_args))->output();
    }

    /**
     * Disables execution of PHP files in Themes.
     *
     *  PHP files in themes directory shouldn't be directly accessible. This is important in case of malware injection as it prevents attacker from directly
     *  accessing infected PHP files
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_php_execution_in_themes
     *     Success: Block Execution In Themes rule has been deployed.
     *
     */
    public function block_php_execution_in_themes($args, $assoc_args) : void {
        (new BlockPhpExecutionInThemes($assoc_args))->output();
    }

    /**
     * Disables execution of PHP files in Uploads.
     *
     *  PHP files in `wp-content/uploads` directory shouldn't be directly accessible. This is important in case of malware injection as it prevents attacker from
     * directly
     *  accessing infected PHP files
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_php_execution_in_uploads
     *     Success: Block Execution In Uploads Directory rule has been deployed.
     *
     */
    public function block_php_execution_in_uploads($args, $assoc_args) : void {
        (new BlockPhpExecutionInUploads($assoc_args))->output();
    }

    /**
     * Disables execution of PHP files in Plugins.
     *
     *  PHP files in `wp-content/plugins` directory shouldn't be directly accessible. This is important in case of malware injection as it prevents attacker
     *  from directly accessing infected PHP files
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_php_execution_in_plugins
     *     Success: Block Execution In Plugins Directory rule has been deployed.
     *
     */
    public function block_php_execution_in_plugins($args, $assoc_args) : void {
        (new BlockPhpExecutionInPlugins($assoc_args))->output();
    }

    /**
     * Disables execution of PHP files in wp-includes directory.
     *
     *  PHP files in `wp-includes` directory shouldn't be directly accessible. This is important in case of malware injection as it prevents attacker
     *  from directly accessing infected PHP files
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_php_execution_in_wp_includes
     *     Success: Block Execution In wp-includes Directory rule has been deployed.
     *
     */
    public function block_php_execution_in_wp_includes($args, $assoc_args) : void {
        (new BlockPhpExecutionInWpIncludes($assoc_args))->output();
    }

    /**
     *  Blocks direct access to sensitive files.
     *
     *  Blocks direct access to readme.html, readme.txt, wp-config.php and wp-admin/install.php files.
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_access_to_sensitive_files
     *     Success: Block Access to Sensitive Files rule has been deployed.
     *
     */
    public function block_access_to_sensitive_files($args, $assoc_args): void {
        (new BlockAccessToSensitiveFiles($assoc_args))->output();
    }

    /**
     *  Blocks direct access to sensitive directories.
     *
     *  Blocks direct access to files in .git, svn and vendor directories
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_access_to_sensitive_directories
     *     Success: Block Access to Sensitive Directories rule has been deployed.
     *
     */
    public function block_access_to_sensitive_directories($args, $assoc_args) : void {
        (new BlockAccessToSensitiveDirectories($assoc_args))->output();
    }

    /**
     *  Blocks direct access to .htaccess
     *
     *  Blocks direct access to .htaccess file
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_access_to_htaccess
     *     Success: Block Access to .htaccess rule has been deployed
     *
     */
    public function block_access_to_htaccess($args, $assoc_args): void {
        (new BlockAccessToHtaccess($assoc_args))->output();
    }

    /**
     *  Blocks author scanning
     *
     *  Author scanning is a common technique of brute force attacks on WordPress. It is used to crack passwords for the known usernames and to gather
     *  additional information about the WordPress itself.
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_author_scanning
     *     Success: Block Author Scanning rule has been deployed.
     *
     */
    public function block_author_scanning($args, $assoc_args) : void {
        (new BlockAuthorScanning($assoc_args))->output();
    }

    /**
     *  Blocks access to XML-RPC
     *
     *  XML-RPC is a remote procedure call which uses XML to encode its calls and HTTP as a transport mechanism. If you want to access and publish to your blog remotely, then you need XML-RPC enabled.
     *  For majority of WordPress installations, XML-RPC is not required and poses a significant security risk.
     *
     * ## OPTIONS
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure block_author_scanning
     *     Success: Block Author Scanning rule has been deployed.
     *
     */
    public function block_access_to_xmlrpc($args, $assoc_args) : void {
        (new BlockAccessToXmlRpc($assoc_args))->output();
    }
}