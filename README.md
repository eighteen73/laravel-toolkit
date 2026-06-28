# eighteen73 Laravel AI Assist

A central package for incorporating AI functionality across eighteen73's Laravel projects.

## Installation

You can install the package via composer. Since this package is currently in development, you can register it locally in your project's `composer.json` using a path repository:

```json
"repositories": [
    {
        "type": "path",
        "url": "../packages/laravel-ai-tools"
    }
]
```

Then require the package:

```bash
composer require eighteen73/laravel-ai-assist
```

The package will automatically register its service provider `Eighteen73\AI\AIAssistServiceProvider` using Laravel's package discovery.

## Usage

*Documentation and features to be added as agents are integrated.*

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
