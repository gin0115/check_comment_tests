<?php

declare(strict_types=1);

/**
 * Tests the default PHP Engine for the view/renderable interface.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package Gin0115\Comment_Moderation
 */

namespace Gin0115\Comment_Moderation\Tests;

use WP_UnitTestCase;

class Test_PHP_Engine extends WP_UnitTestCase {

    /** Runs the passed string through the current comment moderation rulset */
    public function comment_validation(string $string): bool {
        return check_comment(
            'author', 
            'email', 
            'url', 
            $string, 
            '127.0.0.1', 
            'phpunit', 
            'comment'
        );
    }

    /** Reset all options before test */
    public function setUp() {
        update_option( 'comment_previously_approved', 0 );
    }

    public function run_tests($strings) {
        foreach ($strings as $string => $result ) {
            $this->assertEquals($result, $this->comment_validation($string), $this->failure_message($string));
        }
    }

    public function failure_message(string $string): string
    {
        $expression = get_option('moderation_keys');
        return sprintf(
            "Failed testing : %s against %s [native : %s]",
            $string,
            $expression,
            (bool) preg_match( "/{$expression}/", "$string" ) ? 'Non Match' : 'Match'
        );
    }
    
    /** @testdox [WP] Can filter string containing abc with abc* */
    public function test_contains_abc(): void {
        update_option( 'moderation_keys', "abc*" );
        $strings = ['abc' => true, 'abccccc' => true,'abbc' => false];
        $this->run_tests($strings);
    }

    /** @testdox [WP] Can filter string containing single b with ab? */
    public function test_contains_single_b(): void {
        update_option( 'moderation_keys', "ab?" );
        $strings = ['ac' => true, 'abc' => true,'abbc' => false];
        $this->run_tests($strings);
    }

    
}