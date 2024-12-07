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

**Documentation and inputs may not be committed.**
> **Can I copy/redistribute part of Advent of Code?** Please don't. Advent of Code is free to use, not free to copy. If
> you're posting a code repository somewhere, please don't include parts of Advent of Code like the puzzle text or your
> inputs. If you're making a website, please don't make it look like Advent of Code or name it something similar.

### How to jumpstart a new day

```shell
$ (symfony) console make:solver
$ (symfony) console app:grab-input <year> <day>
```

The first command will ask you to specify the year and day, and then create a solver accordingly.
The second one will automatically grab the input for the specified date and put it in the file. You will still need to
manually get and c/c the example input(s).
**To automatically grab your input, the app needs to know your session ID. You will need to get it from your browser and
set it in your `.env.local` file as the `AOC_SESSION_KEY` parameter. Beware not to commit it.**

### Misc

- If there are different test inputs for part one and two, just create two test input files and append the filename with
  `_1` or `_2` to differentiate them
- You must implement the solver constructor and use it to path both the year and day as strings to the parent
  constructor call, or else the `SolverHandler` service will not be able to find it when attempting to use it
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
