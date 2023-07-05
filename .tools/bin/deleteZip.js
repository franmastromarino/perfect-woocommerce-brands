// deleteZip.js
const fs = require('fs');
const path = require('path');

const { pluginName } = require('./helpers/functions');

// Get the base directory of the project
const baseDir = process.cwd();

// Define the zip file
const zipFile = path.join(baseDir, `${pluginName}.zip`);

// Check if the zip file exists

if (fs.existsSync(zipFile)) {
	// Delete the zip file
	fs.unlink(zipFile, (error) => {
		if (error) {
			console.error(`Failed to delete ${zipFile}:`, error);
		} else {
			console.log(`${zipFile} has been deleted.`);
		}
	});
}
