

let Document = require('./Document').Document



exports.QueryClass = class extends Function
{
    #nodelist
    #query
    #name

    VISIBLE = 3001
    HIDDEN  = 3002

    constructor(query, nodelist, name)
    {
        this.query    = query
        this.nodelist = nodelist
        this.name     = name
    }

    _call(value)
    {
        return this.visibility(value)
    }

    visibility(value)
    {
        for(entry of this.nodelist) {
            classes = entry.getAttribute('class').split('/\s+/')
            if (value === this.HIDDEN  &&  classes.includes(this.name)) {
                classes = classes.filter(className => className != this.name)
            }
            if (value === this.VISIBLE && !classes.includes(this.name)) {
                classes.push(this.name)
            }
            entry.setAttribute('class', classes.join(' '))
        }
        return this.query
    }
}
