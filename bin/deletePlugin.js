/**
 * Remove a zip file
 */
const fs = require('fs');

//Get plugin name
const pluginName = process.env.npm_package_name;

//Delete plugn zip
fs.rm(pluginName,{recursive: true}, (err) => {
	err ?? console.log('\x1b[32m%s\x1b[0m', `${pluginName} deleted`);
});
