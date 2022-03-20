<?php

namespace WP_CLI_Secure;

use WP_CLI;
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
use WP_CLI_Secure\SubCommands\FixFileAndDirPermissions;
use WP_CLI_Secure\SubCommands\Flush;

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
 *     $ wp secure disable-directory-browsing --file-path=/some/file/path/.htaccess
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
     * [--file-path=<path>]
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
     * @subcommand disable-directory-browsing
     *
     * @when before_wp_load
     */
    public function disable_directory_browsing($args, $assoc_args) : void {
        (new DisableDirectoryBrowsing($assoc_args))->output();
    }

    /**
     * Disables execution of PHP files in Plugins.
     *
     *  PHP files in `wp-content/plugins` directory shouldn't be directly accessible. This is important in case of malware injection as it prevents attacker
     *  from directly accessing infected PHP files
     *
     * ## OPTIONS
     *
     * <block-part>
     * : Required. accepts: plugins, uploads, includes, themes or all.
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--file-path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     # Apply the block rules for plugins
     *     $ wp secure block-php plugins
     *     Success: Block Execution In Plugins Directory rule has been deployed.
     *
     *     # Apply the block rules for all parts.
     *     $ wp secure block-php all
     *     Success: Block Execution In Plugins Directory rule has been deployed.
     *
     * @when before_wp_load
     *
     * @subcommand block-php-execution
     */
    public function block_php($args, $assoc_args) : void {

		$block_part = $args[0];

	    // Failure first.
	    if ( ! in_array( $block_part,
		    array( 'plugins', 'uploads', 'wp-includes', 'themes', 'all' ),
		    true )
	    ) {
		    WP_CLI::error( sprintf( 'Invalid block part "%s" was provided. Allowed values are "plugins", "uploads", "includes", "themes" or "all"',
			    $block_part ) );
	    }

	    if ( 'all' === $block_part || 'plugins' === $block_part ) {
		    WP_CLI::debug( 'Securing the plugins folder.', 'secure');
		    ( new BlockPhpExecutionInPlugins( $assoc_args ) )->output();
	    }
	    if ( 'all' === $block_part || 'uploads' === $block_part ) {
		    WP_CLI::debug( 'Securing the uploads folder.', 'secure');
		    ( new BlockPhpExecutionInUploads( $assoc_args ) )->output();
	    }
	    if ( 'all' === $block_part || 'wp-includes' === $block_part ) {
		    WP_CLI::debug( 'Securing the includes folder.', 'secure');
		    ( new BlockPhpExecutionInWpIncludes( $assoc_args ) )->output();
	    }
	    if ( 'all' === $block_part || 'themes' === $block_part ) {
		    WP_CLI::debug( 'Securing the themes folder.', 'secure');
		    ( new BlockPhpExecutionInThemes( $assoc_args ) )->output();
	    }
    }

    /**
     *  Blocks direct access to various sensitive files and directories
     *
     *  Blocks direct access to readme.html, readme.txt, wp-config.php and wp-admin/install.php files.
     *
     * ## OPTIONS
     *
     * <block-part>
     * : This option is required. Accepts one of the following values: sensitive-files, sensitive-directories, htaccess, xmlrpc or all.
     *
     * [--remove]
     * : Removes the rule from .htaccess or nginx.conf.
     *
     * [--files]
     * : Custom comma separated file list to block access to. Default is: "readme.html, readme.txt, wp-config.php, wp-admin/install.php"
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--file-path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     # Secure the sensitive files.
     *     $ wp secure block-access files
     *     Success: Block Access to Sensitive Files rule has been deployed.
     *
     *     # Secure all files & directories.
     *     $ wp secure block-access all
     *     Success: Block Access to Sensitive Files rule has been deployed.
     *
     * @subcommand block-access
     *
     * @when before_wp_load
     */
    public function block_access($args, $assoc_args): void {
	    $block_part = $args[0];

	    // Failure first.
	    if ( ! in_array( $block_part, array( 'sensitive-files', 'sensitive-directories', 'htaccess', 'xmlrpc', 'all' ), true ) ) {
		    WP_CLI::error( sprintf( 'Invalid block part "%s" was provided. Allowed values are "files", "directories", "htaccess", "xmlrpc" or "all"', $block_part ) );
	    }

	    if ( 'all' === $block_part || 'sensitive-files' === $block_part ) {
			 WP_CLI::debug( 'Blocking access to the sensitive files.', 'secure');
            (new BlockAccessToSensitiveFiles($assoc_args))->output();
	    }
	    if ( 'all' === $block_part || 'sensitive-directories' === $block_part ) {
		    WP_CLI::debug( 'Blocking access to the directories.', 'secure');
		    ( new BlockAccessToSensitiveDirectories( $assoc_args ) )->output();
	    }
	    if ( 'all' === $block_part || 'htaccess' === $block_part ) {
			 WP_CLI::debug( 'Blocking access to the htaccess.', 'secure');
		    (new BlockAccessToHtaccess($assoc_args))->output();
	    }
	    if ( 'all' === $block_part || 'xmlrpc' === $block_part ) {
			 WP_CLI::debug( 'Blocking access to the xmlrpc.', 'secure');
		    (new BlockAccessToXmlRpc($assoc_args))->output();
	    }
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
     * [--directories]
     * : Custom comma separated directory list to block access to. Default is: "git, svn, vendors, cache"
     *
     * [--output]
     * : Use this option to display the actual code that you can manually copy and paste into some other file
     *
     * [--file-path=<path>]
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
     * @subcommand block-author-scanning
     *
     * @when before_wp_load
     */
    public function block_author_scanning($args, $assoc_args) : void {
        (new BlockAuthorScanning($assoc_args))->output();
    }

    /**
     *  Removes all WP CLI Secure rules
     *
     *  Use this command to remove all deployed security rules. If you are using nginx you need to restart it. If you copied rules manually, this command
     *  will not remove them!
     *
     * ## OPTIONS
     *
     * [--file-path=<path>]
     * : Set a custom path to the file which command should use to write rules into
     *
     * [--server=<server>]
     * : Set a server type. Possible options are "apache" and "nginx". Default is "apache" and all rules are stored in
     * .htaccess file
     *
     * ## EXAMPLES
     *
     *     $ wp secure flush
     *     Success: All security rules have been removed.
     *
     * @when before_wp_load
     */
    public function flush($args, $assoc_args) : void {
        (new Flush($assoc_args))->output();
    }

    /**
     * Verifies WordPress files against WordPress.org's checksums.
     *
     * Downloads md5 checksums for the current version from WordPress.org, and
     * compares those checksums against the currently installed files.
     *
     * It also returns a list of files that shouldn't be part of default WordPress installation.
     *
     * ## EXAMPLES
     *
     * $ wp secure integrity-scan
     *
     * @param $args
     * @param $assoc_args
     *
     * @return void
     *
     * @subcommand integrity-scan
     * @when before_wp_load
     */
    public function integrityscan($args, $assoc_args) : void {
        WP_CLI::runcommand('core verify-checksums');
    }

    /**
     * Disable the file editor in WordPress
     *
     * @subcommand disable-file-editor
     *
     * @param $args
     * @param $assoc_args
     *
     * @when before_wp_load
     *
     * @return void
     */
    public function disable_file_editor($args, $assoc_args) : void {
        WP_CLI::runcommand('config set DISALLOW_FILE_EDIT ' . !isset($assoc_args['remove']));
    }

     /**
     *  Fix all directory and file permissions of the wordpress installation
     *
     *  Use this command to verify that the permissions of all files and directories are set according the wordpress recommendations.
     *  IMPORTANT: Don't use this command if you don't know what you are doing here!
     *
     * ## EXAMPLES
     *
     * $ wp secure fix-permissions
     * Success: All permission are reset to wordpress default.
     *
     * @subcommand fix-permissions
     * @when before_wp_load
     */
    public function fix_permissions($args, $assoc_args) : void {
        (new FixFileAndDirPermissions($assoc_args))->fixPermissions();

        WP_CLI::success("Permission successfully updated.");
    }

    /**
     * Deploys all security rules at once
     *
     * ## EXAMPLES
     *
     * $ wp secure all
     *
     * @param $args
     * @param $assoc_args
     *
     * @return void
     */
    public function all($args, $assoc_args) : void {
        (new DisableDirectoryBrowsing($assoc_args))->output();
        (new BlockPhpExecutionInPlugins($assoc_args))->output();
        (new BlockPhpExecutionInUploads($assoc_args))->output();
        (new BlockPhpExecutionInThemes($assoc_args))->output();
        (new BlockPhpExecutionInWpIncludes($assoc_args))->output();
        (new BlockAccessToXmlRpc($assoc_args))->output();
        (new BlockAccessToHtaccess($assoc_args))->output();
        (new BlockAccessToSensitiveFiles($assoc_args))->output();
        (new BlockAccessToSensitiveDirectories($assoc_args))->output();
        (new BlockAuthorScanning($assoc_args))->output();
        (new FixFileAndDirPermissions())->output();
        $this->disable_file_editor($args, $assoc_args);
    }
}