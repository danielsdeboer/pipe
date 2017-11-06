## Overview

PHP code can be frustratingly opaque, especially when dealing with nested functions. Why not chain those function instead?

```php
$filter = function ($item) { return $item === 'SOME'; };

// Offputting one-liner
echo implode('.', array_filter(explode('-', strtoupper(trim('    some-value'))), $filter))

// Multiple assignments
$value = '    some-value';
$value = trim($value7);
$value = strtoupper($value7);
$value = explode('-', $value7);
$value = array_filter($value7, $filter);
echo implode('.', $value7);

// Easy to read pipe
echo take('    some-value')
    ->trim()
    ->strtoupper()
    ->explode('-', '$$')
    ->array_filter($filter)
    ->implode('.', '$$')
    ->get();

// prints 'SOME'
```

### Installation

Via Composer:

```
composer require aviator/pipe
```

### Testing

Via Composer:

```
composer test
```

### Usage

Get a `Pipe` object:

```php
$value = new Pipe('value');
$value = Pipe::take('value');
$value = take('value');
```

Then you can chain callables:

```php
$value->pipe('strtoupper');
```

And get the mutated value:

```php
echo $value->get();

// prints 'VALUE'
```

The pipe method is chainable:

```php
echo Pipe::take('    value')
    ->pipe('trim')
    ->pipe('strtoupper')
    ->get();
    
// prints 'VALUE'
```

Pipe uses a magic `__call` to redirect other methods to the `pipe` method, so you don't have to use `pipe(...)` at all:

```php
echo Pipe::take('    value')
    ->trim()
    ->strtoupper()
    ->get();
    
// prints 'VALUE'
```

### Arguments

You can use callables with arguments:

```php
echo Pipe::take('value')
    ->str_repeat(3)
    ->strtoupper()
    ->get();
    
// prints 'VALUEVALUEVALUE'
```

`Pipe` will always pass the value you're mutating ('value' in the example above) as the first parameter.

This works most of the time, but since PHP has some unique parameter ordering, there are cases where it doesn't. In these cases you can use the placeholder, by default `$$`, to represent the mutating value.

For example, `implode()`:

```php
echo Pipe::take(['some', 'array'])
    ->implode('.', '$$')
    ->get();
    
// prints 'some.array'
```

Because `implode()` takes the input value as its second parameter, we tell `Pipe` where to put it using '$$'. Then when called the value is swapped in.


### Closures

You may pipe any callable, including a closure:

```php
$closure = function ($item) { return $item . '-postfixed'; };

echo Pipe::take('value')
    ->pipe($closure)
    ->get();

// prints 'value-postfixed'
```

## Other Stuff

### License

This package operates under the MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.

### Thanks

This is largely based on [Sebastiaan Luca's idea](https://github.com/sebastiaanluca/laravel-helpers) and his `Pipe\Item` class. 
