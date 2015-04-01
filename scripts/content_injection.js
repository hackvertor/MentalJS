window.addEventListener('DOMContentLoaded', function() {        
       var html = document.getElementById('MentalRender').textContent, js=MentalJS();
       js.init({dom:true});
       js.parse({options:{eval:true},code:'this.sandboxedFunction=function(str){document.body.innerHTML=str}'});
       this.sandboxedFunction$(html);
}, false);
