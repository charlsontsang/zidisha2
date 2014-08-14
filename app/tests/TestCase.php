<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../bootstrap/testing.php';
    }

    public function setup()
    {
        parent::setUp();

        \Config::set('mail.enabled', false);
    }

    public static function setupBeforeClass()
    {
        @session_start();
        parent::setupBeforeClass();
    }
}
