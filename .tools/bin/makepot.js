// makepot.js
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const { baseDir, pluginName, pluginFiles } = require('./helpers/functions');

// Define the output directory and file
const outputDir = path.join(baseDir, 'languages');
const outputFile = path.join(outputDir, `${pluginName}.pot`);

// Create the output directory if it doesn't exist
if (!fs.existsSync(outputDir)) {
	fs.mkdirSync(outputDir);
}

// Define the exclude list
const excludeFiles = ['node_modules', 'languages', 'vendor', 'tests'];

// Filtered include files
const includeFiles = pluginFiles.filter((file) => !excludeFiles.includes(file));

// Construct the command
const command = `php .tools/vendor/wp-cli/wp-cli/php/boot-fs.php i18n make-pot ${baseDir} ${outputFile} --include=${includeFiles.join(
	','
)}`;

try {
	// Run the command
	execSync(command, { stdio: 'inherit' });
	console.log(`.pot file has been generated at ${outputFile}`);
} catch (error) {
	console.error('An error occurred while generating the .pot file:', error);
}
