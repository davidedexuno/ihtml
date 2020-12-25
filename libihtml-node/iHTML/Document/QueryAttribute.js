

let Document = require('./Document').Document



exports.QueryAttribute = class extends Function
{
    #nodelist
    #query
    #name

    // TODO:
    CONTENT = 2003;
    // DISPLAY = 2004;
    NONE    = 2005;
    // function display($value)
    // VISIBLE = 2006;
    // HIDDEN  = 2007;
    // function visibility($value)

    constructor(query, nodelist, name)
    {
        this.query    = query
        this.nodelist = nodelist
        this.name     = name
    }

    _call(value)
    {
        return this.content(value)
    }

    content(value)
    {
        for (entry of this.nodelist) {
            entry.setAttribute(this.name, value)
        }
        return this.query
    }

    display(value)
    {
        for (entry of this.nodelist) {
            if(value === this.NONE) {
                entry.removeAttribute(this.name)
            }
            else {
                throw new Exception('attribute/not NONE Not yet implemented.')
            }
            entry.setAttribute('style', style)
        }
        return this.query
    }

    visibility(value)
    {
        throw new Exception('attribute/visibiliy Not yet implemented.')
    }
}
