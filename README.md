# regex-line-length linter for arc

regex-line-length is a linter for use with [Phabricator](http://phabricator.org)'s `arc` command
line tool. This linter is able to identify lines that exceed a certain length while ignoring lines
that match a list of regexes.

## Installation

### Project-specific

Add this repository as a git submodule.

    git submodule init
    git submodule add <url for this repo>

Your `.arcconfig` should list `arc-regex-line-length-lint` in the `load` configuration:

    {
      "load": [
        "path/to/arc-regex-line-length-lint"
      ]
    }

### Global

Clone this repository to the same directory where `arcanist` and `libphutil` are globally located.
Your directory structure will look like so:

    arcanist/
    libphutil/
    arc-proselint/

Your `.arcconfig` should list `arc-regex-line-length-lint` in the `load` configuration (without a
path):

    {
      "load": [
        "arc-regex-line-length-lint"
      ]
    }

## Usage

Create a `.arclint` file in the root of your project and add the following content:

    {
      "line-length": {
        "type": "regex-line-length"
      },
    }

Feel free to change the include/exclude regexes to suit your project's needs.

### Configuration options

Line length can be configured by providing a `max-line-length` number:

    {
      "line-length": {
        "type": "regex-line-length",
        "max-line-length": 100
      },
    }

Lines can be ignored if they match a given list of regexes. In the following example we ignore lines
that include a url.

    {
      "line-length": {
        "type": "regex-line-length",
        "ignore-line-regexes": [
          "(https?://)"
        ]
      },
    }

## License

Licensed under the Apache 2.0 license. See LICENSE for details.
