<?php

namespace Aviator\Pipe\Test;

use Aviator\Pipe\Pipe;
use PHPUnit\Framework\TestCase;

class PipeTest extends TestCase
{
    /** @test */
    public function all_creation_methods_are_equivalent ()
    {
        $pipes = [
            $pipe1 = new Pipe('string'),
            $pipe2 = Pipe::take('string'),
            $pipe3 = take('string'),
        ];

        foreach ($pipes as $pipe) {
            $this->assertInstanceOf(Pipe::class, $pipe);
            $this->assertSame('string', $pipe->get());
        }
    }

    /** @test */
    public function it_transforms_values_using_callable_strings ()
    {
        $value = take('string')
            ->pipe('strtoupper')
            ->get();

        $this->assertSame('STRING', $value);
    }

    /** @test */
    public function it_transforms_values_using_closures ()
    {
        $value = take('string')
            ->pipe(function (string $value) {
                return 'prefixed-' . $value;
            })
            ->get();

        $this->assertSame('prefixed-string', $value);
    }

    /** @test */
    public function it_accepts_and_uses_pipe_parameters ()
    {
        $value = take(['some', 'test', 'values'])
            ->pipe('implode', '.')
            ->get();

        $this->assertSame('some.test.values', $value);
    }

    /** @test */
    public function it_replaces_placeholders_with_values ()
    {
        $withoutPlaceholder = take('some.test.values')
            ->pipe('explode', '.')
            ->get();

        $withPlaceholder = take('some.test.values')
            ->pipe('explode', '.', '$$')
            ->get();

        $this->assertSame(['.'], $withoutPlaceholder);
        $this->assertSame(['some', 'test', 'values'], $withPlaceholder);
    }

    /** @test */
    public function it_accepts_and_uses_an_optional_custom_placeholder ()
    {
        $value = take('st.ri.ng', '**')->explode('.', '**')->get();

        $this->assertSame(['st', 'ri', 'ng'], $value);
    }

    /** @test */
    public function it_defers_undefined_methods_to_the_pipe_method ()
    {
        $stringValue = take('string')
            ->strtoupper()
            ->get();

        $arrayValue = take('some.value')
            ->explode('.', '$$')
            ->get();

        $this->assertSame('STRING', $stringValue);
        $this->assertSame(['some', 'value'], $arrayValue);
    }

    /** @test */
    public function it_can_transform_values_in_multiple_steps ()
    {
        $value = take('some.test.values')
            ->pipe('explode', '.', '$$')
            ->pipe('array_map', function ($item) {
                return $item . '-test';
            }, '$$')
            ->pipe('implode', '/', '$$')
            ->get();

        $this->assertSame('some-test/test-test/values-test', $value);
    }

    /** @test */
    public function using_pipe_and_using_magic_call_are_equivalent ()
    {
        $piped = take('some.test.values')
            ->pipe('explode', '.', '$$')
            ->pipe('array_map', function ($item) {
                return $item . '-test';
            }, '$$')
            ->pipe('implode', '/', '$$')
            ->get();

        $called = take('some.test.values')
            ->explode('.', '$$')
            ->array_map(function ($item) {
                return $item . '-test';
            }, '$$')
            ->implode('/', '$$')
            ->get();

        $this->assertSame($piped, $called);
    }

    /** @test */
    public function documentation_examples_work_as_expected ()
    {
        $value1 = Pipe::take('    value')
            ->pipe('trim')
            ->pipe('strtoupper')
            ->get();

        $this->assertSame('VALUE', $value1);

        $value2 = Pipe::take('    value')
            ->trim()
            ->strtoupper()
            ->get();

        $this->assertSame('VALUE', $value2);

        $value3 = Pipe::take('value')
            ->str_repeat(3)
            ->strtoupper()
            ->get();

        $this->assertSame('VALUEVALUEVALUE', $value3);

        $value4 = Pipe::take(['some', 'array'])
            ->implode('.', '$$')
            ->get();

        $this->assertSame('some.array', $value4);

        $closure = function ($item) {
            return $item . '-postfixed';
        };
        $value5 = Pipe::take('value')
            ->pipe($closure)
            ->get();

        $this->assertSame('value-postfixed', $value5);

        $filter = function ($item) {
            return $item === 'SOME';
        };

        $value6 = implode('.', array_filter(explode('-', strtoupper(trim('    some-value'))), $filter));

        $this->assertSame('SOME', $value6);

        $value7 = '    some-value';
        $value7 = trim($value7);
        $value7 = strtoupper($value7);
        $value7 = explode('-', $value7);
        $value7 = array_filter($value7, $filter);
        $value7 = implode('.', $value7);

        $this->assertSame($value6, $value7);

        $value8 = take('    some-value')
            ->trim()
            ->strtoupper()
            ->explode('-', '$$')
            ->array_filter($filter)
            ->implode('.', '$$')
            ->get();

        $this->assertSame($value7, $value8);
    }
}
