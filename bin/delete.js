/**
 * Remove folders and files
 *
 * Remove command is used to remove folders and files and called from package.json
 */

const fs = require('fs');

//Get arguments
const myArgs = process.argv.slice(2);

//Foreach arguments/folders
myArgs.forEach((element) => {
	//Delete folder/file
	fs.rm(element,{recursive: true}, (err) => {
		err ?? console.log('\x1b[32m%s\x1b[0m', `${element} deleted`);
	});
});

