const fs = require('fs');
const path = require('path');
const p = path.join(__dirname, 'workoutdetails.js');
const src = fs.readFileSync(p, 'utf8');
try {
  // eslint-disable-next-line no-new-func
  new Function(src);
  console.log('OK: syntax valid');
} catch (e) {
  console.error('SYNTAX ERROR:', e.message);
  process.exitCode = 1;
}

