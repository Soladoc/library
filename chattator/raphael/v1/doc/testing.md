# Testing

## Test database

- Pro API keys
  - bb1b5a1f-a482-4858-8c6b-f4746481cffa
  - 52d43379-8f75-4fbd-8b06-d80a87b2c2b4
- Member API keys
  - 123e4567-e89b-12d3-a456-426614174000
  - 9ea59c5b-bb75-4cc9-8f80-77b4ce851a0b
- Three (3) users without API keys
  - 1 public pro
  - 1 private pro
  - 1 member

## I/O expected JSON format

I/O json files are compared as JSON objects (objects are unordered).

A special object can be used instead of any value for input, validation and extraction of runtime-known values:

```json
{
  "$fmt_number": "%*ld"
}
```

where the value at key `$fmt` is a `scanf` conversion specifier for output, or a `printf` conversion specifier for input.

When inputting format JSON, the formatted value is parses as JSON, in order to create a JSON object of the appropriate type.

```json
{
  "$fmt_number": "%d"
}
```

Becomes with arg `177`:

```json
177
```

Another example:

```json
{
  "$fmt": "%s"
}
```

Becomes with arg `177`:

```json
"177"
```

More complex example:

```json
{
  "$fmt": {
    "a":"%s",
    "b":"%d"
  }
}
```

Becomes, given the arguments `"hello"` and `14`:

```json
{
  "a": "hello",
  "b": 14
}
```
