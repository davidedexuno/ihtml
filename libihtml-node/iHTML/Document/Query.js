





exports.Query = class
{
    #query

    constructor(domdocument, modules, selector)
    {
        this.query = domdocument.window.document.querySelectorAll(selector)
        for (moduleName in modules) { module = modules[moduleName]
            if (this[moduleName] ?? null) {
                throw new Error(`Modifier name \`${moduleName}\` is a reserved name.`);
            }
            this.moduleName = module
            this.moduleName.setList(this.query)
        }
    }

    getResults()
    {
        return this.query
    }

    empty()
    {
        return this.query.length == 0;
    }

    attr(name, value = null)
    {
        if (arguments.length == 1) {
            return new QueryAttribute(this, this.query, name)
        }
        ( new QueryAttribute(this, this.query, name) )(value)
        return this
    }

    style(name, value = null)
    {
        if (arguments.length == 1) {
            return new QueryStyle(this, this.query, name)
        }
        ( new QueryStyle(this, this.query, name) )(value)
        return this
    }

    className(name, value = null)
    {
        if (arguments.length == 1) {
            return new QueryClass(this, this.query, name)
        }
        ( new QueryClass(this, this.query, name) )(value)
        return this
    }

    jsonLD(name, value = null)
    {
        if (arguments.length == 1) {
            return new QueryJson(this, this.query, name)
        }
        ( new QueryJson(this, this.query, name) )(value)
        return this
    }












}
