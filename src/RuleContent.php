<?php

namespace WP_CLI_Secure;

class RuleContent {


    public array $content;
    public array $templateVars;

    /**
     * @param array $content The rule file's content.
     * @param array $arguments The CLI arguments.
     */
    public function __construct(array $content, array $templateVars ) {
        $this->content = $content;
        $this->templateVars = $templateVars;
    }

    /**
     * @return bool
     */
    public function getContent() : array {
        $result = '';
        $templateContent = implode( PHP_EOL, $this->content );

        foreach ( $this->templateVars as $var => $replacements ) {
            foreach ( $replacements as $replacement ) {
                $result .= str_replace( sprintf( '{{%s}}', $var ), $replacement, $templateContent );
            }
        }

        $result = explode( PHP_EOL, $result );

        return $result;
    }
}