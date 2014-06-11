// V8 Compiler test suite
// -----------------------------------------------------------------------------
module("compiler", MentalJSTestEnv);

v8test('assignment', 90);
v8test('countoperation', 60);
v8test('function-call', 4);
v8test('globals', 16);
v8test('literals', 37);
v8test('literals-assignment', 17);
v8test('loopcount', 9);
v8test('loops', 1);
v8test('objectliterals', 10);
v8test('property-simple', 2);
v8test('short-circuit', 30);
v8test('simple-bailouts', 14);
v8test('simple-binary-op', 1);
v8test('simple-global-access', 7);
v8test('thisfunction', 1);
v8test('this-property-refs', 6);
v8test('unary-add', 29);


//
// V8 JS Unit tests
// -----------------------------------------------------------------------------
module("mjsunit", MentalJSTestEnv);

v8test("for", 1);
v8test("for-in", 36);
//v8test("function", 36);
v8test("multiple-return", 3);
v8test("prototype", 9);
v8test("var", 6);
