const { execSync } = require('child_process');
const { pluginFiles } = require('./helpers/functions');

// Define the exclude list
const excludeFiles = ['node_modules', 'languages', 'vendor', 'tests', 'build'];

// Filtered include files
const includeFiles = pluginFiles.filter((file) => !excludeFiles.includes(file));

// Construct the command
const command = `php .tools/vendor/bin/phpcbf --extensions=php ${includeFiles.join(
	' '
)}`;

try {
	// Run the command
	execSync(command, { stdio: 'inherit' });
	console.log(`phpcs ${reportType} report has been generated`);
} catch (error) {
	if (error.status === 1) {
		console.log('PHP CodeSniffer found coding standard violations.');
	} else {
		console.error(
			'An error occurred while generating the phpcs ${reportType} report.',
			error.status
		);
	}
}
