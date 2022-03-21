Feature: Fix file & directory permissions.

  Background:
    Given a WP install

  Scenario: Fix all permissions on a faulty installation.

    # Start by making a mess.
    When I run `chmod 777 -R ./`
    And I run `stat -c "%a" index.php wp-admin`
    Then STDOUT should be:
      """
      777
      777
      """
    # TODO make this one working, I'm getting a warning `PHP Warning:  chmod(): Operation not permitted`
#    When I run `wp secure fix-permissions`
#    And I run `stat -c "%a" index.php wp-admin`
#    Then STDOUT should be:
#      """
#      666
#      755
#      """