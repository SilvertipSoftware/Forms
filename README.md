# Forms

## About

The `SilvertipSoftware\Forms` package allows you to easily construct forms for Eloquent models. It
tries to mirror the `form_for` helpers in Rails, so documents/guides for that functionality should
help here as well.

## Installation

Require the `silvertipsoftware/forms` package in your `composer.json` and update your
dependencies:
```sh
$ composer require silvertipsoftware/form
```

The easiest way to use the `Form` package is to register the helper class with the Laravel app
container inside a service provider with:
```php
$this->app->singleton(FormHelper::class, function ($app) {
    return new FormHelper($app);
});
```

and then create an alias in `config/app.php`:
```php
    'aliases' => [
        ...
        'Form' => SilvertipSoftware\Forms\Facades\Form::class,
    ]
```

## Usage

### Starting a Form


## License

Released under the MIT License, see [LICENSE](LICENSE).
