# ihtml
iHTML - incremental HTML

A very experimental template engine (written in php so far)

## Usage

Applies ccs on template and outputs to file:
```shell
ihtml <template> <ccs> [-o <file>]
```

Applies code on template and outputs to file:
```shell
ihtml <template> -r "<code>" [-o <file>]
```

Applies stdin on template and outputs to file:
```shell
ihtml <template> [-o <file>]
<code>
```

Compiles the project:
```shell
ihtml -p <project> [-o <file>]
```

Opens a server on a project:
```shell
ihtml -p <project> -s
```

## Examples

See examples/.

## Advantages
* One language for everything (se `use cases` below)
* consistency with the rest of environment - one language everywhere, JAVASCRIPT, CSS AND HERE
* No need one more (maybe) language, SELECTORS EVERYWHERE, DOM EVERYWHERE
* Layout TOTALLY separated from code, a designer can manage the final html file
* No need to prepare an HTML for integration
* No need to prepare HTML for new block - inheritance is EVERYWHERE, customizability is EVERYWHERE
* Better HTML files, more readable. "Lorem ipsum" is the way.

## Use cases
* HTML Inheritance
* Multilanguage support (site multilanguage structure and labels system)
* Site Structure (pages, sections, etc...)
* Modularization (separate ads, sidebar, ecc...)
* Template engines code injection (Twig, Smarty, etc...)
* CMS (Markdown, BBcode, HTML, text plain, etc...)
* Minify (WTF?!!)

## TODO
* add ld+json navigation support
* add url parts support (img[src], a[href], etc...)
* move to a REAL html5 parser (like the Chrome one)

