<?php

namespace Eighteen73\Toolkit\Tests\Unit;

use Eighteen73\Toolkit\Testing\AgencyStandards;
use Eighteen73\Toolkit\Tests\TestCase;
use RuntimeException;

class AgencyStandardsTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir().'/agency_standards_test_'.uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob("{$this->tempDir}/*") ?: []);
            rmdir($this->tempDir);
        }
        parent::tearDown();
    }

    public function test_throws_exception_when_pest_arch_is_not_available(): void
    {
        if (function_exists('arch')) {
            $this->markTestSkipped('Pest arch is available in this environment.');
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pest architecture testing functions are not available');

        AgencyStandards::register();
    }

    public function test_passes_when_composer_json_is_valid(): void
    {
        $file = $this->tempDir.'/composer.json';
        file_put_contents($file, json_encode([
            'require' => [
                'php' => '^8.2',
            ],
            'require-dev' => [
                'ergebnis/composer-normalize' => '^2.52',
            ],
            'config' => [
                'allow-plugins' => [
                    'ergebnis/composer-normalize' => true,
                ],
                'platform' => [
                    'php' => '8.2.0',
                ],
            ],
        ], JSON_PRETTY_PRINT));

        AgencyStandards::verifyComposer($file);
        $this->assertTrue(true);
    }

    public function test_fails_when_normalize_is_missing(): void
    {
        $file = $this->tempDir.'/composer.json';
        file_put_contents($file, json_encode([
            'require' => ['php' => '^8.2'],
            'config' => [
                'platform' => ['php' => '8.2.0'],
            ],
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ergebnis/composer-normalize must be present');

        AgencyStandards::verifyComposer($file);
    }

    public function test_fails_when_normalize_is_not_allowed_plugin(): void
    {
        $file = $this->tempDir.'/composer.json';
        file_put_contents($file, json_encode([
            'require' => ['php' => '^8.2'],
            'require-dev' => ['ergebnis/composer-normalize' => '^2.52'],
            'config' => [
                'platform' => ['php' => '8.2.0'],
            ],
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ergebnis/composer-normalize must be allowed under config.allow-plugins');

        AgencyStandards::verifyComposer($file);
    }

    public function test_fails_when_platform_php_is_missing(): void
    {
        $file = $this->tempDir.'/composer.json';
        file_put_contents($file, json_encode([
            'require' => ['php' => '^8.2'],
            'require-dev' => ['ergebnis/composer-normalize' => '^2.52'],
            'config' => [
                'allow-plugins' => [
                    'ergebnis/composer-normalize' => true,
                ],
            ],
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('config.platform.php must be specified in composer.json.');

        AgencyStandards::verifyComposer($file);
    }

    public function test_fails_when_platform_php_does_not_satisfy_require_php(): void
    {
        $file = $this->tempDir.'/composer.json';
        file_put_contents($file, json_encode([
            'require' => ['php' => '^8.3'],
            'require-dev' => ['ergebnis/composer-normalize' => '^2.52'],
            'config' => [
                'allow-plugins' => [
                    'ergebnis/composer-normalize' => true,
                ],
                'platform' => [
                    'php' => '8.2.0',
                ],
            ],
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not satisfy the require.php constraint');

        AgencyStandards::verifyComposer($file);
    }
}
