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
ihtml template -r "<code>" [-o file]
```

Applies stdin on template and outputs to file:
```shell
ihtml template [-o file]
<CODE>
```

Compiles the project:
```shell
ihtml -p project
```
## Examples

See examples/.

## Use cases
* HTML Inheritance
* Multilanguage support (site structure and labels system)
* Site Structure (pages, sections, etc...)
* Modularization (Separate ads, sidebar, ecc...)
* Template engines code injection (Twig, Smarty, etc...)

## TODO
* add ld+json navigation support
* add url parts support (img[src], a[href], etc...)

