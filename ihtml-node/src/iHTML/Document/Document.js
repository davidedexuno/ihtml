'use strict'

const Query = require('./Query').Query
const jsdom = require('jsdom'); const { JSDOM } = jsdom
//Ccs = iHTML.Ccs.Ccs
//require('Css')
var glob = require("glob")
var path = require('path')
var fs = require('fs')
exports.Document = class extends Function
{
    #domdocument
    #modifiers = []

    constructor(html)
    {
        super('return arguments.callee._call.apply(arguments.callee, arguments)')

        html = path.resolve(html)
        if (!fs.existsSync(html)) {
            throw new Error(`File ${html} not found.`)
        }
        this.domdocument = new JSDOM( fs.readFileSync(html) )
        this.loadModifiers()
        // LOAD INTERNAL CCS
        // <link rel="contentsheet" href="..."> ...
        for(let result of this('link[rel="contentsheet"][href]').getResults()) {
            //ccs = new Ccs(path.resolve(html, result.getAttribute('href')))
            //ccs.applyTo(this)
            result.parentNode.removeChild(result)
        }
        // <content> ... </content> ...
        for(let result of this('content').getResults()) {
            //ccs = new Ccs()
            //ccs.setContent(result.textContent, path.dirname(html))
            //ccs.applyTo(this)
            result.parentNode.removeChild(result)
        }
        // <ELEM content="..."> ...
        for(let result of this('[content]').getResults()) {
            // TODO
        }
    }


    // implements $document('SELECTOR') ...
    _call(selector)
    {
        return new Query(this.domdocument, this.modifiers, selector)
    }


    // final rendering
    async render(output = null)
    {
        // render modifiers final changes
        for(modifier of this.modifiers) {
            modifier.render()
        }
        // returns / prints / writes document
        switch (output) {
            case null:
                return (new XMLSerializer()).serializeToString(this.domdocument)
            break;
            case STDOUT:
                print (new XMLSerializer()).serializeToString(this.domdocument)
            break;
            default:
                out_dir = path.dirname(output)
                if (!!out_dir && !fs.existsSync(out_dir)) {
                    fs.mkdirSync(out_dir, {mode: 0o777, recursive: true})
                }
                await fs.writeFile(output, (new XMLSerializer()).serializeToString(this.domdocument) )
            break;
        }
    }
    
    loadModifiers()
    {
        for(modifierFile of glob.sync(__dirname+'/Modifiers/*.class.php')) {
            require(modifierFile)
            modifierName = modifierFile.replace(__dirname + '/Modifiers/', '').replace('.class.php', '')
            classname = `iHTML.Document.Modifiers.${modifierName}Modifier`;
            modifier = new classname(this.domdocument)
            this.modifiers[ modifier.queryMethod() ] = modifier;
        }
    }
}
