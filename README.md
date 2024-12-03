# Advent of Code PHP

## Prerequisites

- PHP 8.4

## How to install

```bash
$ (symfony) composer install
```

## How to use

Documentation, inputs and solvers follow a `Year{YYYY}/Day{DD}` naming convention, with the year as 4 digits and the day
as 2.

### Adding a new solver if none exists

- **`{day}`: single digits must be prefixed with a `0`, ie. `3` => `03` etc.**
- Resources:
    - add part one and part two of the statement in `src/Resources/doc/Year{year}/Day{day}.md`
    - write the test input in a `{day}.txt` in `src/Resources/input/Year{year}/test/` if it's the same test input for
      both parts, or as two separate files, appended with `_1` or `_2` to differentiate them
    - write your input in a `{day}.txt` in `src/Resources/input/Year{year}/`
- Add a new service in `src/ConundrumSolver/Year{year}/`  (by copying model class `ExampleConundrumSolver.php`), name it
  `Day{day}ConundrumSolver` and make sure to extend `AbstractConundrumSolver`
    - You must implement the constructor and use it to path both the year and day as strings to the parent constructor
      call, or else the `SolverHandler` service will not be able to find it when attempting to use it
- Implement your logic in both `partOne()` and `partTwo()` and have them return your result
- You can override `warmup()` to warmup class properties, manage input for both parts...

### Displaying the results

Just run:

```bash
$ (symfony) console app:resolve-conundrums 2024 1
```

With the year as your first argument and the day as the second one (both `1` and `01` are valid options).
You can use the option `-T/--with-test-input` if you want to test your logic on the test input.

### Using services

Services and entities will be added as and when they become relevant for use in multiple solvers.
