

let Document = require('./Document').Document
const styleParse = require('style-parser')


exports.QueryStyle = class extends Function
{
    #nodelist
    #query
    #name

    //CONTENT = 2003
    //DISPLAY = 2004
    NONE    = 2005
    VISIBLE = 2006
    HIDDEN  = 2007

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

            entry.style[ this.name ] = value

        }
        return this.query
    }
    
    display(value)
    {
        $valueStyle = parse(value);
        for (entry of this.nodelist) {

            if (entry.style[ this.name ]) {
                let newRules = []
                for(n in entry.style) { v = entry.style[n]
                    if(n == this.name)
                        if(value === this.NONE)
                            {}
                        else
                            newRules += valueStyle
                    else
                        newRules[ n ] = entry.style[ n ]
                }
                entry.style = newRules
            } else {
                if(value === this.NONE)
                    {}
                else
                    entry.style += valueStyle
            }

        }
        return this.query
    }
}
