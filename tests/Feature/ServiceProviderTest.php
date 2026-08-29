<?php

namespace Eighteen73\Toolkit\Tests\Feature;

use Eighteen73\Toolkit\Boost\Install\Agents\Gemini;
use Eighteen73\Toolkit\Tests\TestCase;
use Laravel\Boost\Boost;

class ServiceProviderTest extends TestCase
{
    public function test_it_registers_gemini_agent_with_boost(): void
    {
        $agents = Boost::getAgents();
        $this->assertArrayHasKey('eighteen73-gemini', $agents);
        $this->assertEquals(Gemini::class, $agents['eighteen73-gemini']);
    }
}
