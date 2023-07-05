const fs = require('fs');
const { consoleSuccess } = require('./helpers/console');

//Get arguments
const myArgs = process.argv.slice(2);

//Foreach arguments/folders
myArgs.forEach((element) => {
	//Delete folder/file
	fs.rm(element, { recursive: true }, (err) => {
		err ?? consoleSuccess(`${element} deleted`);
	});
});
