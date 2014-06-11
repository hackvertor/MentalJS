MentalJS
========

MentalJS is a JavaScript parser and sandbox. It whitelists JavaScript code by adding a "$" suffix to variables and accessors.

Usage
=====

```javascript
var js = MentalJS();
js.parse({
    code:'1+1',
    result:function(result){ 
      alert(result) 
    }
});
```

