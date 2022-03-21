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

        if ( empty( $this->templateVars ) ) {
            return $this->content;
        }

        $result = '';
        $templateContent = implode( PHP_EOL, $this->content );

        foreach ( $this->templateVars as $var => $replacements ) {
            $tmp_result = $templateContent;
            foreach ( $replacements as $key => $replacement ) {
                if ( preg_match( '/.+\/.+/', $key ) ) {
                    $tmp_result = implode( PHP_EOL, $replacement );
                    $tmp_result = str_replace( '{{file}}', $key, $tmp_result );
                } else {
                    $tmp_result = str_replace( sprintf( '{{%s}}', $key ), $replacement, $tmp_result );
                }
            }
            $result .= $tmp_result;
        }

        $result = explode( PHP_EOL, $result );

        return $result;
    }
}