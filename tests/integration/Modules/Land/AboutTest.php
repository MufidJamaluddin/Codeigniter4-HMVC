<?php

use CodeIgniter\Test\FeatureTestCase;
use CodeIgniter\Debug\Timer;

class AboutTest extends FeatureTestCase
{
    private $timer;

    public function setUp(): void
    {
        parent::setUp();

        // Extra code to run before each test
        $this->timer = new Timer();
    }

    public function testOpenAboutPage()
    {
        $this->timer->start('open_Aboutpage', strtotime('-2 seconds'));
        
        $result = $this->call('get', '/land/about');

        // 2 seconds on load page
        $this->assertCloseEnough(2, $this->timer->getElapsedTime('open_Aboutpage'));

        $result->assertStatus(200);
    }

    public function tearDown(): void
    {
        // Extra code to run after each test
        unset($this->timer);

        parent::tearDown();
    }
}