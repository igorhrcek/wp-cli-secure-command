Feature: Stop author scanning.

  Background:
    Given a WP install

  Scenario: add php blocking rules to the htaccess and remove it afterwards.

    When I run `wp secure block-author-scanning`
    Then STDOUT should be:
      """
      Success: Block Author Scanning rule has been deployed.
      """
    And the .htaccess file should exist
    And the .htaccess file should contain:
      """
      marker BLOCK AUTHOR SCANNING start
      """