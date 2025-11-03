<?php
/**
 * Basic PHPUnit test placeholder.
 */

use PHPUnit\Framework\TestCase;

class RevoltSliderCloneTest extends TestCase {
    public function test_plugin_constants_defined() {
        $this->assertTrue( defined( 'REVOLT_SLIDER_CLONE_VERSION' ), 'Plugin version constant should be defined.' );
    }
}
