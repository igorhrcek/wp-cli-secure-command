Feature: Disable or enable file edits.

  Background:
    Given a WP install

  Scenario: Disallow file edits.

    When I run `wp secure disable-file-editor`
    Then the wp-config.php file should contain:
    """
    DISALLOW_FILE_EDIT
    """

    When I run `wp config get DISALLOW_FILE_EDIT`
    Then STDOUT should be a number
    And STDOUT should be:
    """
    1
    """

  Scenario: Allow file edits.

    When I run `wp secure disable-file-editor --remove`
    Then the wp-config.php file should contain:
    """
    DISALLOW_FILE_EDIT
    """

    When I run `wp config get DISALLOW_FILE_EDIT`
    Then STDOUT should be:
    """

    """
