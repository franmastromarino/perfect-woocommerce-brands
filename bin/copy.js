const fs = require('fs');
const { copyFileFromTo, consoleSuccess, consoleError } = require('./helpers');

//Get arguments
const myArgs = process.argv.slice(2);


//Foreach arguments/folders
try{
    copyFileFromTo(myArgs[0], myArgs[1]);
    consoleSuccess('File copied successfully')
} catch(e) {
    consoleError('Can not copy the file, internationalization not found.')
}
