window.addEventListener('DOMContentLoaded', function() {        
       var html = document.getElementById('MentalRender').textContent, js=MentalJS();
       js.init({
            dom:true,
            parseInnerHTML : function(dirty) {
                var config = {
                    ADD_TAGS: ['script'],
                    ADD_ATTR: ['onclick', 'onmouseover','onerror']
                };
                DOMPurify.addHook('uponSanitizeElement', function(node, data) {
                    if (data.tagName === 'script') {
                        var script = node.textContent;
                        if (!script || 'src' in node.attributes
                            || 'href' in node.attributes
                            || 'xlink:href' in node.attributes) {
                                return node.parentNode.removeChild(node)
                        }
                        try {
                            // Pass scripts to MentalJS
                            var mental = MentalJS().parse(
                                {
                                    options: {
                                        eval: false,
                                        dom:true
                                    },
                                 code:script
                                }
                            );
                            return node.textContent = mental;
                        } catch (e) {
                            return node.parentNode.removeChild(node);
                        }
                    }
                });
                DOMPurify.addHook('uponSanitizeAttribute', function(node, data) {
                    if (data.attrName.match(/^on\w+/)) {
                        var script = data.attrValue;
                        try {
                            // Pass scripts to MentalJS
                            return data.attrValue = MentalJS().parse(
                                {
                                    options: {
                                        eval: false,
                                        dom: true
                                    },
                                    code: script
                                }
                            );
                        } catch (e) {
                            return data.attrValue = '';
                        }
                    }
                });
                return DOMPurify.sanitize(dirty, config);
            }
       });
       js.parse({options:{eval:true},code:'this.sandboxedFunction=function(str){document.body.innerHTML=str}'});
       this.sandboxedFunction$(html);
}, false);
