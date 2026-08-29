<?php

namespace Eighteen73\Toolkit\Testing;

use Composer\Semver\Semver;
use RuntimeException;

class AgencyStandards
{
    /**
     * Register all standard agency expectations for Pest.
     *
     * @param  array<string|int, string|array<int, string>>  $ignores
     */
    public static function register(array $ignores = []): void
    {
        if (! function_exists('arch')) {
            throw new RuntimeException('Pest architecture testing functions are not available. Please ensure pestphp/pest (^3.0) is installed.');
        }

        self::registerArchRules($ignores);
        self::registerComposerRules();
    }

    /**
     * @param  array<string|int, string|array<int, string>>  $ignores
     */
    protected static function registerArchRules(array $ignores = []): void
    {
        $laravelPreset = arch()->preset()->laravel();
        $securityPreset = arch()->preset()->security();

        if (empty(getenv('CI'))) {
            $laravelPreset->ignoring(['dd', 'ddd', 'dump', 'ray']);
        }

        $laravelIgnores = [];
        $securityIgnores = [];

        if (array_is_list($ignores)) {
            $laravelIgnores = $ignores;
            $securityIgnores = $ignores;
        } else {
            if (! empty($ignores['security'])) {
                $securityIgnores = is_array($ignores['security']) ? $ignores['security'] : [$ignores['security']];
            }
            if (! empty($ignores['laravel'])) {
                $laravelIgnores = is_array($ignores['laravel']) ? $ignores['laravel'] : [$ignores['laravel']];
            }

            foreach ($ignores as $key => $value) {
                if (in_array($key, ['observers', 'rules', 'security', 'laravel'], true)) {
                    continue;
                }
                if (is_array($value)) {
                    $laravelIgnores = array_merge($laravelIgnores, $value);
                } elseif (is_string($value)) {
                    $laravelIgnores[] = $value;
                }
            }
        }

        if (! empty($laravelIgnores)) {
            $laravelPreset->ignoring($laravelIgnores);
        }

        if (! empty($securityIgnores)) {
            $securityPreset->ignoring($securityIgnores);
        }

        arch('no debugging functions are left in the codebase')
            ->skip(empty(getenv('CI')), 'Only enforced in CI')
            ->expect(['dd', 'dump', 'ray', 'var_dump'])
            ->not->toBeUsed();

        arch('observers')
            ->expect('App\Observers')
            ->toHaveSuffix('Observer')
            ->ignoring($ignores['observers'] ?? []);

        arch('rules')
            ->expect('App\Rules')
            ->toImplement('Illuminate\Contracts\Validation\ValidationRule')
            ->ignoring($ignores['rules'] ?? []);
    }

    protected static function registerComposerRules(): void
    {
        if (! function_exists('test')) {
            return;
        }

        test('composer.json complies with agency standards', function () {
            AgencyStandards::verifyComposer();
        });
    }

    /**
     * Verify composer.json against agency standards.
     */
    public static function verifyComposer(?string $composerPath = null): void
    {
        $composerPath = $composerPath ?? (
            function_exists('base_path')
                ? base_path('composer.json')
                : getcwd().'/composer.json'
        );

        if (! file_exists($composerPath)) {
            throw new RuntimeException("composer.json was not found at: {$composerPath}");
        }

        $contents = file_get_contents($composerPath);
        $composer = json_decode((string) $contents, true);

        if (! is_array($composer)) {
            throw new RuntimeException('composer.json is not valid JSON.');
        }

        if (function_exists('expect')) {
            self::verifyComposerWithPest($composer);
        } else {
            self::verifyComposerDirectly($composer);
        }
    }

    /**
     * @param  array<string, mixed>  $composer
     */
    protected static function verifyComposerWithPest(array $composer): void
    {
        $hasNormalizeDev = isset($composer['require-dev']['ergebnis/composer-normalize']);
        $hasNormalizeReq = isset($composer['require']['ergebnis/composer-normalize']);
        $isAllowedPlugin = ($composer['config']['allow-plugins']['ergebnis/composer-normalize'] ?? false) === true;

        expect($hasNormalizeDev || $hasNormalizeReq)
            ->toBeTrue('ergebnis/composer-normalize must be present in composer.json (typically under require-dev).');

        expect($isAllowedPlugin)
            ->toBeTrue('ergebnis/composer-normalize must be allowed under config.allow-plugins in composer.json.');

        $platformPhp = $composer['config']['platform']['php'] ?? null;

        expect($platformPhp)
            ->not->toBeNull('config.platform.php must be specified in composer.json.')
            ->not->toBeEmpty('config.platform.php cannot be empty in composer.json.');

        $platformPhpStr = (string) $platformPhp;
        $requirePhp = (string) ($composer['require']['php'] ?? '');

        expect($requirePhp)->not->toBeEmpty('require.php must be specified in composer.json.');

        $normalizedPlatformPhp = preg_match('/^\d+\.\d+$/', $platformPhpStr)
            ? $platformPhpStr.'.0'
            : $platformPhpStr;

        if (class_exists(Semver::class)) {
            $isCompatible = Semver::satisfies($normalizedPlatformPhp, $requirePhp);

            expect($isCompatible)
                ->toBeTrue("The configured config.platform.php ({$platformPhpStr}) does not satisfy the require.php constraint ({$requirePhp}).");
        }
    }

    /**
     * @param  array<string, mixed>  $composer
     */
    protected static function verifyComposerDirectly(array $composer): void
    {
        $hasNormalizeDev = isset($composer['require-dev']['ergebnis/composer-normalize']);
        $hasNormalizeReq = isset($composer['require']['ergebnis/composer-normalize']);
        $isAllowedPlugin = ($composer['config']['allow-plugins']['ergebnis/composer-normalize'] ?? false) === true;

        if (! ($hasNormalizeDev || $hasNormalizeReq)) {
            throw new RuntimeException('ergebnis/composer-normalize must be present in composer.json (typically under require-dev).');
        }

        if (! $isAllowedPlugin) {
            throw new RuntimeException('ergebnis/composer-normalize must be allowed under config.allow-plugins in composer.json.');
        }

        $platformPhp = $composer['config']['platform']['php'] ?? null;

        if (empty($platformPhp)) {
            throw new RuntimeException('config.platform.php must be specified in composer.json.');
        }

        $platformPhpStr = (string) $platformPhp;
        $requirePhp = (string) ($composer['require']['php'] ?? '');

        if (empty($requirePhp)) {
            throw new RuntimeException('require.php must be specified in composer.json.');
        }

        $normalizedPlatformPhp = preg_match('/^\d+\.\d+$/', $platformPhpStr)
            ? $platformPhpStr.'.0'
            : $platformPhpStr;

        if (class_exists(Semver::class) && ! Semver::satisfies($normalizedPlatformPhp, $requirePhp)) {
            throw new RuntimeException("The configured config.platform.php ({$platformPhpStr}) does not satisfy the require.php constraint ({$requirePhp}).");
        }
    }
}
