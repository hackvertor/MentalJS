window.addEventListener('DOMContentLoaded', function() {        
       var html = document.getElementById('MentalRender').textContent, js=MentalJS();
       js.parse({global:true,thisObject:window,options:{eval:true,dom:true},code:'this.sandboxedFunction=function(str){document.body.innerHTML=str}'});
       this.sandboxedFunction$(html);
}, false);
