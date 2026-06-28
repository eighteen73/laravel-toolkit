<?php

namespace Eighteen73\AI\Tests\Feature;

use Eighteen73\AI\Boost\Install\Agents\Gemini;
use Eighteen73\AI\Tests\TestCase;
use Laravel\Boost\Boost;

class ServiceProviderTest extends TestCase
{
    public function test_it_registers_gemini_agent_with_boost(): void
    {
        $agents = Boost::getAgents();
        $this->assertArrayHasKey('gemini', $agents);
        $this->assertEquals(Gemini::class, $agents['gemini']);
    }
}
