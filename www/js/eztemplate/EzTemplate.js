/**
 * 
 */



class EzTemplate {
	
	
    constructor( container ) {
		
		if (typeof container == 'string')
			this.container = document.getElementById( container );
		else
			this.container = container;
		
        this.reAttributeBinding = /^\[.*\]$/;

        this.reExpressionBinding = /{{(.*?)}}/g;

		this.parentTemplate = null;
		this.parentNode = null;
		

        this.partial = null;
        this.originalNodes = null;

        this.vars = {};

        this.attributeVariables = [];

        this.expressions = [];
        
        this.subTemplates = [];

    }
    
    
    static createInstanceSubTemplate( parentTemplate, node, templateHtml ) {
		let sub = new EzTemplate( node );
        
        // not used, example for future usage
		sub.setParentTemplate( this );
		 sub.setParentNode( node );
        
        sub.setVars( parentTemplate.getVars() );
        
        sub.loadHtml( templateHtml );

        parentTemplate.helper_emptyNode( node );
        
        node.eztemplate = sub;
        
        return sub;
	}
    
    	
	setParentTemplate( t ) { this.parentTemplate = t; }
	setParentNode( n ) { this.parentNode = n; }
	

    setVar(key, val) { this.vars[key] = val; }
    getVars() { return this.vars; }
    setVars(vars) { this.vars = vars; }
    
    
    loadNode(node) {
		if (!node)
			node =  this.container.childNodes;
		
//        console.log(node.constructor.name);
        if (node.constructor && node.constructor.name == 'NodeList') {
            let nl = [];

            node.forEach(function(obj) {
                nl.push(obj.cloneNode(true));
            });

            this.originalNodes = nl;
        }
        else if (Array.isArray(node)) {
            let nl = [];

            for (let i in node) {
                nl.push(node[i].cloneNode(true));
            }

            this.originalNodes = nl;
		}
        else {
            this.originalNodes = [ node.cloneNode(true) ];
        }
        
    }
    
    cloneNode() {
		
		let result = [];
		
		for(let i in this.originalNodes) {
			result.push( this.originalNodes[i].cloneNode(true) );
		}
		
		return result;
	}

    
    loadHtml(html) {
        var s = document.createElement('span');
        s.innerHTML = html;
        
        this.originalNodes = [];
        s.childNodes.forEach(function(obj) {
            this.originalNodes.push(obj.cloneNode(true));
        }.bind(this));

    }
    
    parse() {
        if (!this.partial) {
            console.error('template partial not loaded');
            return;
        }
        
        if (this.partial.length == 0) {
            console.error('EzTemplate.parse(): template partial empty. loadNode or loadHtml called?');
            return;
        }
        
        this.attributeVariables = [];
        this.expressions = [];
        this.subTemplates = [];

        // TODO: parse..
        if (!this.partial.nodeType) {
            for (var i in this.partial) {
                this._parse(this.partial[i]);
            }
        }
        else {
            this._parse(this.partial);
        }
    }
    
    _parse(node) {

        if (this._containsExpression(node)) {
            this.expressions.push(node);
        }
        
        
		// handle ez-template
        if (node.nodeName && node.nodeName.startsWith('EZ-')) {
			EzTemplateLoader.loadTemplate( node.nodeName, function( tpl, opts ) {
				let sub = EzTemplate.createInstanceSubTemplate( this, opts.parentNode, tpl );
                
                this.subTemplates.push( sub );
				
			}.bind(this), { parentNode: node });
        }
        
        // handle attributes
        if (node.attributes) {
            var l = node.attributes.length;


            for (var i = 0; i < l; i++) {
                var attr = node.attributes.item(i);

                if (!attr.nodeName)
                    continue;


                // ez-for-handling => create partial
                if (attr.nodeName == 'ez-for') {
					let sub = EzTemplateFor.createInstance( this, node );
                    
                    this.subTemplates.push( sub );
                }

				// ez-if-handling => create partial
                if (attr.nodeName == 'ez-if') {
                    let sub = EzTemplateIf.createInstance( this, node );
                    
                    this.subTemplates.push( sub );
                }


                // fill attribute mapping
                if (attr.nodeName.match(this.reAttributeBinding)) {
                    this.attributeVariables.push(attr);
                }

                if (this._containsExpression(attr)) {
                    this.expressions.push(attr);
                }

            }
        }

        if (node.hasChildNodes) {
            for (var i in node.childNodes) {
                this._parse(node.childNodes[i]);
            }
        }
    }
    
    /**
	 * getVarValue( path ) - get template-var by path, ie: my.var[0].key
	 * 
	 */
    getVarValue(path) {
		
		// replace array's with dots, items[0].value => items.0.value
		path = path.replace(/\[\s*(\d+)\s*\]/g, '.$1.');	// explode array's
		path = path.replace(/^\./, '');						// remove leading dot
		path = path.replace(/\.$/, '');						// remove ending dot
		path = path.replace(/\.+/, '.')						// remove double dots
		
		// expode to tokens
        let tokens = path.split('.');

		// lookup
        let curVar = this.vars;
        
//        console.log( path );

        for (let i in tokens) {

            var n = tokens[i];

            let varName = n;

            if (!curVar[varName]) {
                return null;
            }

            curVar = curVar[varName];
        }

        return curVar;
    }
    
    setVarValue( vars, path, value ) {
		
		// replace array's with dots, items[0].value => items.0.value
		path = path.replace(/\[\s*(\d+)\s*\]/g, '.$1.');	// explode array's
		path = path.replace(/^\./, '');						// remove leading dot
		path = path.replace(/\.$/, '');						// remove ending dot
		path = path.replace(/\.+/, '.')						// remove double dots
		
//		console.log(path);
		
		let tokens = path.split('.');
//		console.log( tokens );
		
    	let subvar = vars;
    	
    	for(let i=0; i < tokens.length; i++) {
			let t = tokens[i];
			
			if (i == tokens.length -1)
				subvar[t] = value;
			else if (!subvar[ t ])
				subvar[ t ] = {};
			subvar = subvar[ t ];
		}
		
		return vars;
	}
    
    /**
	 * applyVars() - apply's attribute binding to [attr-name]-attributes
	 * 
	 */
    applyVars() {
        for (var i in this.attributeVariables) {
            var att = this.attributeVariables[i];

            // get varname
            var attrName = att.nodeName;
            attrName = attrName.substr(1);
            attrName = attrName.substr(0, attrName.length - 1);

            var varName = att.value;
            
            // get value
            var v = this.getVarValue(varName);

			// contenthtml? => insert html, can break stuff..
			if (attrName == 'contenthtml') {
				this.helper_emptyNode( att.ownerElement );
				att.ownerElement.innerHTML = v;
			}
			// text? => insert as text-node (escapes special chars)
			else if (attrName == 'contenttext') {
				this.helper_emptyNode( att.ownerElement );
				att.ownerElement.appendChild( document.createTextNode(v) );
			}
            // set attribute
			else {
            	att.ownerElement.setAttribute(attrName, v);
        	}
            
            // eztemplate? => set var
            if (att.ownerElement.eztemplate) {
				att.ownerElement.eztemplate.setVar( attrName, v );
			}
        }
    }
    
    /**
	 * applyExpression() - apply's {{ }}-expressions
	 */
    applyExpressions() {
//        console.log(this.expressions);
        for (let i in this.expressions) {
            let o = this.expressions[i];
            
	
	        let v = o.nodeValue;
	
	        let matches = v.match(this.reExpressionBinding);
	        if (matches) {
	
	            for (let matchNo in matches) {
	                let result = this.execExpression(matches[matchNo]);
	
	                v = v.replaceAll(matches[matchNo], result);
	            }
	
	            o.nodeValue = v;
	        }
        }
    }
    
    /**
	 * _containsExpression() - checks if nodeValue contains an {{ }}-expression
	 */
    _containsExpression(node) {
		if (node && node.nodeValue) {
			var v = node.nodeValue;

            if (v.match(this.reExpressionBinding))
                return true;
		}
		
		return false;
    }
    
    /**
	 * execExpression() - execute's {{ }}-expression
	 */
    execExpression(code) {

        // remove {{ }}
        if (code.startsWith('{{')) {
            code = code.substr(2);
            code = code.substr(0, code.length - 2);
        }

        // code for importing tpl_vars
        let js_tplvars = '';
        for (let v in this.vars) {
            if (typeof v == 'string' && v.match(/^[a-zA-Z_]+$/)) {
                js_tplvars += 'let ' + v + ' = ' + JSON.stringify(this.vars[v]) + ';\n';
            }
        }

        // build js
        code = js_tplvars + "\n\n return " + code + ';';


        // console.log( code );
        let result='';
        
        // exec
        try {
            result = eval('(function() { ' + code + ' }.bind( this ))');
            result = result();
        }
        catch (err) {
            result = 'Error: ' + err.message;
            console.error(err);
        }

        return result;
    }
    
    
    applySubtemplates() {
		
		// console.log( this.subTemplates.length );
		
		for(let i in this.subTemplates) {
			this.subTemplates[i].reset();
			this.subTemplates[i].update();
		}
		
	}

    
    
    helper_emptyNode( node ) {
		while (node && node.hasChildNodes() ) {
			node.removeChild( node.childNodes[0] );
		}
	}
    
    
    serializeVars( opts ) {
		
		opts = opts ? opts : {};
		
		let container = opts.container ? opts.container : this.container;
		
        // TODO: get vars from dom
        let r = {};
        
        this._serializeVars( container, r );
		
		return r;
    }
    _serializeVars( container, vars, opts ) {
		
		for(let i in container.childNodes) {
			let node = container.childNodes[i];
//			console.log( node );

			if (node.nodeName == 'INPUT' || node.nodeName == 'TEXTAREA') {
				
				// set node.value
				if (node.name) {
					this.setVarValue( vars, node.name, node.value );
				}
				
				
				let l = node.attributes.length;
	            for (var ac = 0; ac < l; ac++) {
	                let att = node.attributes.item(ac);
	                
	                if ( ! att.nodeName.match( this.reAttributeBinding ) )
	                	continue;
	            	
	                // get varname
	            	let  attrName = att.nodeName;
	            	attrName = attrName.substr(1);
	            	attrName = attrName.substr(0, attrName.length - 1);
	            	
	            	
	            	let varname = att.value;
	            	
	
					if (attrName == 'contenthtml') {
						this.setVarValue( vars, varname, node.innerHTML );
					}
					else if (attrName == 'contenttext') {
						this.setVarValue( vars, varname, node.textContent );
					}
					else if (attrName == 'value') {
						this.setVarValue( vars, varname, node.value );
					}
	            }
            }
			
			
			if (node.childNodes && node.childNodes.length > 0) {
				let tpl = node.eztemplate ? node.eztemplate : this;
				
				let sv = tpl._serializeVars(node, vars);
				
				// ez-for? => set in var
				// else merge
				for(let v in sv) {
					vars[v] = sv[v];
				}
			}
		}
		
		return vars;
	}
    
    
	reset() {
	}
	
	
	checkTemplatesLoaded(callback) {
		// check if all templates are loaded, if not, wait
		if (EzTemplateLoader.xhrCounter > 0) {
			EzTemplateLoader.clearCallbackXhrFinished();
			
			EzTemplateLoader.addCallbackXhrFinished(function() {
				if (EzTemplateLoader.xhrCounter == 0) {
					callback();
				}
			}.bind(this));
			return false;
		}
		
		return true;
	}

    
    build( opts ) {
		
		this.partial = this.cloneNode();
	
        this.parse();
        
        // check if templates are loaded
        let r = this.checkTemplatesLoaded(function() {
			if (opts && opts.update_on_templates_loaded) {
				this.update();
			}
			// rebuild
			else {
				this.build();
			}
        }.bind(this) );
        
        // templates not loaded? => wait for callback
        if (!r) {
			return false;
		}

		// apply's []-attributes
        this.applyVars();
        
        // apply's {{ }}-vars
        this.applyExpressions();
        
        // ez-for, ez-if, ...
        this.applySubtemplates();
        

        return this.partial;
    }
    
    isTopTemplate() {
		if (!this.parentTemplate)
			return true;
		else
			return false;
	}
    
    update( ) {
		// loadNode() & loadHtml not called? => lets go
		if (this.isTopTemplate() && !this.partial) {
			this.loadNode();
		}

	
		// build it
		let nodes = this.build( { update_on_templates_loaded: true });
		
		// false? => templates not yet loaded
		if (nodes === false) {
			return;
		}
		
		var c = this.container;
		
		this.helper_emptyNode( c );
		
		// set nodes
		for(let i in nodes) {
			c.appendChild( nodes[i] );
		}
		
		// trigger EzTemplate.updated-event for top template
		if (this.isTopTemplate()) {
			let evt = new Event('EzTemplate.updated');
			window.dispatchEvent( evt );
		}
	}
}








class EzTemplateFor extends EzTemplate {
	
	constructor() {
		super();
		
		this.counter = 0;
		this.items = [];
		
		this.nameItem = null;
		this.nameKey = null;
		this.nameCounter = null;
		
		this.originalNodes = [];
		
	}
	
	static createInstance( parentTemplate, node ) {
		let sub = new EzTemplateFor();
		
		let arrayItem = node.attributes['ez-for'].value;
                    
        sub.setParentTemplate( parentTemplate );
        sub.setParentNode( node );
        
        sub.setVars( parentTemplate.getVars() );
        
        sub.loadNode( node.childNodes );

        if ( node.attributes['ez-item'] ) {
            sub.setVar(node.attributes['ez-item'], parentTemplate.getVarValue(arrayItem));
        }
        
        // empty node, rendering by EzTemplateFor
        parentTemplate.helper_emptyNode( node );
        
        node.eztemplate = sub;
        
        return sub;
	}
	
	reset() {
		this.helper_emptyNode( this.parentNode );
		this.counter = 0;
	}
	
	loadNode(node) {
//        console.log(node.constructor.name);
        
        this.originalNodes = [];
        
        if (node.constructor.name == 'NodeList') {
            node.forEach(function(obj) {
                this.originalNodes.push(obj.cloneNode(true));
            }.bind(this));
        }
        else {
			this.originalNodes.push( node.cloneNode(true) );
        }
        
//        console.log( this.originalNodes );
    }
    
    _resetPartial() {
		let l = [];
		
		for(let i in this.originalNodes) {
			l.push( this.originalNodes[i].cloneNode(true) );
		}
		
		this.partial = l;
	}
	
	
    _serializeVars( container, vars, opts ) {
		opts = opts ? opts : {};
		
//		console.log( container );
		
		// ez-for="..."-value
		let nameItemsCollection = null;
		if ( this.parentNode.attributes['ez-for'] )
			nameItemsCollection = this.parentNode.attributes['ez-for'].value;
		
		
		// not found/set? => skip
		if (!nameItemsCollection) {
			console.error( 'Error: EzTemplateFor._serializeVars, ez-for=value not found');
			return vars;
		}
		
		
		if (typeof opts.itemCounter == 'undefined')
			opts.itemCounter = -1;
		
		for(let i in container.childNodes) {
			let node = container.childNodes[i];
			
			// check if node._itemCounter is set. Items are added in order, so last _itemCounter is the valid one
			if (typeof node._itemCounter != 'undefined')
				opts.itemCounter = node._itemCounter;
			
			
			if (opts.itemCounter >= 0 && (node.nodeName == 'INPUT' || node.nodeName == 'TEXTAREA')) {
				let l = node.attributes.length;
	            for (var ac = 0; ac < l; ac++) {
	                let att = node.attributes.item(ac);
	                
	                if ( ! att.nodeName.match( this.reAttributeBinding ) )
	                	continue;
	            	
	                // get varname
	            	let  attrName = att.nodeName;
	            	attrName = attrName.substr(1);
	            	attrName = attrName.substr(0, attrName.length - 1);
	            	
	            	// determine path => replace 'ez-item'-prefix by ez-for="name[<counter>]"
	            	let prefix = '';
	            	let varName = att.nodeValue;
	            	if (att.nodeValue.startsWith( this.nameItem + '.' )) {
	            		prefix = this.nameItem + '.';
	            		varName = varName.substr( prefix.length );
            		}
	            	let varPath = nameItemsCollection + '['+opts.itemCounter+'].' + varName;
	            	
	            	
					if (attrName == 'contenthtml') {
						this.setVarValue( vars, varPath, node.innerHTML );
					}
					else if (attrName == 'contenttext') {
						// TODO..
						this.setVarValue( vars, varPath, node.textContent );
					}
					else if (attrName == 'value') {
						this.setVarValue( vars, varPath, node.value );
					}
	            }
            }
			
			
			if (node.childNodes && node.childNodes.length > 0) {
				
				let sv = this._serializeVars(node, vars, opts);
				
				// ez-for? => set in var
				// else merge
				for(let v in sv) {
					vars[v] = sv[v];
				}
			}
		}
		
		return vars;
	}
    
    createRecord( record, opts ) {
		if (!record)
			record = {};
		
		let t = new EzTemplate();
		
		t.setVars( this.vars );
		
		if (this.nameItem)
			t.setVar( this.nameItem, record );
		if (this.nameCounter)
			t.setVar( this.nameCounter, this.counter );
		
		if (this.nameKey)
			t.setVar( this.nameKey, opts.iteratorKey );
		
		t.loadNode( this.cloneNode() );
		
//		t.parse();
//		t.applyVars();
//		t.applyExpressions();
		
		let nodes = t.build();
		
		for(let i in nodes) {
			nodes[i]._itemCounter = this.counter;
			
			this.parentNode.appendChild(nodes[i]);
		}
		
		this.subTemplates.push( t );
    
		this.counter++;
	}
	
    build() {
		
		this.items = this.parentTemplate.getVarValue( this.parentNode.attributes['ez-for'].nodeValue );
		if ( this.parentNode.attributes['ez-item'] )
			this.nameItem    = this.parentNode.attributes['ez-item'].nodeValue;
		
		if ( this.parentNode.attributes['ez-key'] )
			this.nameKey     = this.parentNode.attributes['ez-key'].nodeValue;
		
		if ( this.parentNode.attributes['ez-counter'] )
			this.nameCounter = this.parentNode.attributes['ez-counter'].nodeValue;
		
		
		for(let i in this.items) {
			
			this.createRecord( this.items[i], { iteratorKey: i } );
		}
		
    }
}


class EzTemplateIf extends EzTemplate {

	constructor() {
		super();
		
		
	}
	
	
	static createInstance(parentTemplate, node) {
		let sub = new EzTemplateIf();
                    
        sub.setParentTemplate( parentTemplate );
        sub.setParentNode( node );
        
        sub.setVars( parentTemplate.getVars() );
        
        sub.loadNode( node.childNodes );
        
        // empty node, rendering by EzTemplateIf
        parentTemplate.helper_emptyNode( node );
        
        node.eztemplate = sub;
        
        return sub;
	}
	
	
	build() {
		this.partial = this.cloneNode();
		
		let jscode = this.parentNode.attributes['ez-if'].nodeValue;
		
		let r = this.execExpression( '{{'+jscode+'}}' );
		
		if (r === '1' || r === 1) {
			r = true;
		}
		
//		console.log(r, jscode, this.vars);
		if (r === true) {
//			console.log( this.originalNodes );
			
			if (this.parentNode.hasChildNodes() == false) {
				this.partial = this.originalNodes;
				let c = super.build();
				for(let i in c) {
					this.parentNode.appendChild(c[i]);
				}
			}
		}
		else {
			// false? => hide
			this.helper_emptyNode( this.parentNode );
		}
	}
}




