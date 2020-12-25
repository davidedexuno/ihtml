

const fs = require("fs")
const path = require('path')
const Document       = require('../Document/Document').Document
//const Ccs            = require('../Ccs/Ccs')          .Ccs

exports.Project = class
{

	#root;

	#implicit;

	#templates;

	constructor(project)
	{
		let project_dir = path.resolve(project)
	
		// PROJECT VALIDATION
		if(!fs.existsSync(project_dir))                   throw new Error('Project not found.')
		if(!fs.lstatSync(project_dir).isDirectory())      throw new Error('Project is not a dir.')
		if(!fs.existsSync(project_dir + '/project.json')) throw new Error('Project file not found.')

		project = JSON.parse(fs.readFileSync(project_dir + '/project.json'))
		if(!project)                                      throw new Error('Malformed project file.')
		if(!project.implicit)                             throw new Error('Malformed project file.')
		if(!project.templates)                            throw new Error('Malformed project file.')
		if(!Array.isArray(project.templates))             throw new Error('Malformed project file.')

		this.root = project_dir;

		this.implicit = project.implicit;

		this.templates = project.templates;
	}

	getRoot()
	{
	
		return this.root
	
	}

	getTemplates()
	{
	
		return this.templates
	
	}

	render(out)
	{
		root = this.root
		out_dir = path.resolve( out )

		if(fs.existsSync(out_dir) && ( !fs.lstatSync(out_dir).isDirectory() || !fs.accessSync(out_dir, fs.constants.W_OK) )) throw new Error('Error creating output folder.')
		if(!fs.existsSync(out_dir) && !fs.mkdirSync(out, {mode: 0o777, recursive: true}))                                    throw new Error('Error creating output folder.')
		
		// SOLVE IMPLICIT FILES
		this.templates.forEach(template => {
			if(template.output[template.output.length-1] == '/') template.output += this.implicit })


		// COMPILE ALL FILES
		for(let template of this.templates)
		{
			document = new Document( path.resolve(root, template.html) )

				ccs = new Ccs( path.resolve(root, template.apply) )
				ccs.applyTo(document)

			document.render( path.resolve(out_dir, template.output) )
		}
	}
}

