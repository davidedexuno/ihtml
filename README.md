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
ihtml -p <project> -s [-t <static files dir>]
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
* Sanitize (removing every not-allowed content in a tag)
* Minify (WTF?!!)

## TODO
* add javascript on* attributes support
* add `content` attribute support
* add CSS vars() function support
* add dependency tool on project
* add full website example to be used ad unit test
* add `blog posts` example
* check @import loop
* add white-space support for inline CSSs and JSs
* add `code` rule support
* support for border, margin, padding, wikitext
* add support for rule `attributes: A B, C D`
* add SASS/SCSS example
* add ld+json navigation support
* add url parts support (link[href], script[src], a[href], img[src], source[src], video[poster] and other URI attributes - https://www.w3.org/TR/2017/REC-html52-20171214/fullindex.html#attributes-table)
* add `[srcset]` support
* add `<style>` support
* move to a REAL html5 parser (like the Chrome one)

