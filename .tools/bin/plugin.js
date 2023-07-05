const fs = require('fs');

const {
	pluginFolder,
	pluginFiles,
	deleteThisFrom,
	copyFromTo,
} = require('./helpers/functions');

const { consoleSuccess } = require('./helpers/console');

/**
 * Execute:
 *	Delete old pluginFolder folder
 *	Create new pluginFolder folder
 *	Copy files and folders in pluginFolder folder
 */
fs.rm(pluginFolder, { recursive: true }, (err) => {
	//Show status
	err ?? consoleSuccess(`${pluginFolder} deleted`);
	//Create pluginName folder
	fs.mkdir(pluginFolder, { recursive: true }, (err) => {
		//Show status
		err ?? consoleSuccess(`${pluginFolder} created`);

		//Copy files and folders in pluginName folder
		for (const file of pluginFiles) {
			copyFromTo(file, pluginFolder);
		}

		//Delete files and folders in pluginName folder
		deleteThisFrom('/vendor/bin', pluginFolder);
	});
});
