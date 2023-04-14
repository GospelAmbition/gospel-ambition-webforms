<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'gospel-ambition-webforms/gospel-ambition-webforms.php' );

        $this->assertContains(
            'gospel-ambition-webforms/gospel-ambition-webforms.php',
            get_option( 'active_plugins' )
        );
    }
}
