const fs = require('fs');
const path = require('path');
const baseDir = process.cwd();

// Check if .wc.json exists
if (!fs.existsSync(path.join(baseDir, '.wc.json'))) {
	return;
}

const wcJson = require(path.join(baseDir, '.wc.json'));

const {
	pluginName: pluginOldName,
	deleteThisFrom,
	cloneDirectory,
} = require('./helpers/functions');

const pluginNewName = wcJson.name;
const pluginNewContentReplacements = wcJson.replacements;
const pluginOldFolder = './.plugin/' + pluginOldName;
const pluginNewFolder = './.plugin/' + pluginNewName;
const pluginOldFile = pluginNewFolder + '/' + pluginOldName + '.php';
const pluginNewFile = pluginNewFolder + '/' + pluginNewName + '.php';

const { consoleSuccess } = require('./helpers/console');

cloneDirectory(pluginOldFolder, pluginNewFolder);

// Delete files and folders in pluginNewName folder
deleteThisFrom(pluginNewFolder, '/vendor/bin');
deleteThisFrom(pluginNewFolder, '/vendor_packages/wp-license-client.php');
deleteThisFrom(
	pluginNewFolder,
	'/vendor_packages/wp-notice-plugin-required.php'
);

// Read the new plugin file
let pluginNewFileContent = fs.readFileSync(pluginOldFile, 'utf-8');

// Sort keys by length in descending order
let sortedKeys = Object.keys(pluginNewContentReplacements).sort(
	(a, b) => b.length - a.length
);

// Replace lines to white space
for (let i of sortedKeys) {
	let key = i;
	if (i.startsWith('/')) {
		// Remove the starting and ending slashes and create a new RegExp object
		key = new RegExp(i.slice(1, i.lastIndexOf('/')));
	}

	pluginNewFileContent = pluginNewFileContent.replace(
		key,
		pluginNewContentReplacements[i]
	);
}
// Delete the old plugin file
fs.unlinkSync(pluginOldFile);
// Write the new content to the new plugin file
fs.writeFileSync(pluginNewFile, pluginNewFileContent, 'utf-8');

consoleSuccess('WooCommerce Plugin Compiled!');
