<?php

use CodeIgniter\Test\FeatureTestCase;
use CodeIgniter\Debug\Timer;

class DashboardTest extends FeatureTestCase
{
    private $timer;

    public function setUp(): void
    {
        parent::setUp();

        // Extra code to run before each test
        $this->timer = new Timer();
    }

    public function testOpenDashboardPage()
    {
        $this->timer->start('open_homepage', strtotime('-2 seconds'));
        
        $result = $this->call('get', '/admin/dashboard');

        // 2 seconds of load page
        $this->assertCloseEnough(2, $this->timer->getElapsedTime('open_homepage'));

        $result->assertStatus(200);
    }

    public function tearDown(): void
    {
        // Extra code to run after each test
        unset($this->timer);

        parent::tearDown();
    }
}