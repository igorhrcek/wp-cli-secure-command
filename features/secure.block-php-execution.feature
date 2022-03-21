Feature: Manage WordPress comments

  Background:
    Given a WP install

  Scenario: add php blocking rules to the htaccess or nginx config file.

    When I run `wp secure block-php-execution plugins`
    Then STDOUT should be:
      """
      Success: Block Execution In Plugins Directory rule has been deployed.
      """
    And the .htaccess file should exist
    And the .htaccess file should contain:
      """
      marker BLOCK PHP EXECUTION IN PLUGINS start
      """

    # TODO fix this one, it's not working right now.
#    When I run `wp secure block-php-execution plugins`
#    Then STDOUT should be:
#      """
#      Warning: The rule already exist in the file
#      """
#    And the return code should be 0

    When I run `wp secure block-php-execution plugins --remove`
    Then STDOUT should be:
      """
      Success: Block Execution In Plugins Directory rule has been removed.
      """
    And the .htaccess file should not contain:
      """
      marker BLOCK PHP EXECUTION IN PLUGINS start
      """