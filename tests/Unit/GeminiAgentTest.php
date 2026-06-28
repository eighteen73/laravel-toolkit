<?php

namespace Eighteen73\AI\Tests\Unit;

use Eighteen73\AI\Boost\Install\Agents\Gemini;
use Eighteen73\AI\Tests\TestCase;
use Laravel\Boost\Install\Enums\Platform;

class GeminiAgentTest extends TestCase
{
    public function test_name_and_display_name(): void
    {
        $agent = $this->app->make(Gemini::class);

        $this->assertEquals('gemini', $agent->name());
        $this->assertEquals('Gemini CLI', $agent->displayName());
    }

    public function test_transform_guidelines_escapes_mentions_only_in_foundational_context(): void
    {
        $agent = $this->app->make(Gemini::class);

        $markdown = "## Foundational Context\nThis is @foo/bar.\n## Another Section\nThis is @hello/world.";
        $expected = "## Foundational Context\nThis is \\@foo/bar.\n## Another Section\nThis is @hello/world.";

        $this->assertEquals($expected, $agent->transformGuidelines($markdown));
    }

    public function test_system_detection_config(): void
    {
        $agent = $this->app->make(Gemini::class);

        $darwinConfig = $agent->systemDetectionConfig(Platform::Darwin);
        $this->assertEquals('command -v gemini', $darwinConfig['command']);

        $windowsConfig = $agent->systemDetectionConfig(Platform::Windows);
        $this->assertEquals('cmd /c where gemini 2>nul', $windowsConfig['command']);
    }

    public function test_project_detection_config(): void
    {
        $agent = $this->app->make(Gemini::class);
        $config = $agent->projectDetectionConfig();

        $this->assertEquals(['.gemini'], $config['paths']);
        $this->assertEquals(['AGENTS.md'], $config['files']);
    }

    public function test_paths_and_mcp_config(): void
    {
        $agent = $this->app->make(Gemini::class);

        $this->assertEquals('.gemini/settings.json', $agent->mcpConfigPath());
        $this->assertEquals('AGENTS.md', $agent->guidelinesPath());
        $this->assertEquals('.agents/skills', $agent->skillsPath());

        $httpMcpConfig = $agent->httpMcpServerConfig('https://example.com/mcp');
        $this->assertEquals('npx', $httpMcpConfig['command']);
        $this->assertEquals(['-y', 'mcp-remote', 'https://example.com/mcp'], $httpMcpConfig['args']);
    }
}
