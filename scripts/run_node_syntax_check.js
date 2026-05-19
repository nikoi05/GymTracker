const fs = require('fs');
const p = process.argv[2];
const src = fs.readFileSync(p, 'utf8');
try {
  // eslint-disable-next-line no-new-func
  new Function(src);
  console.log('OK');
} catch (e) {
  console.log('ERR');
  console.log(String(e.message));
  if (e.lineNumber != null) console.log('line', e.lineNumber);
  if (e.columnNumber != null) console.log('col', e.columnNumber);
}

