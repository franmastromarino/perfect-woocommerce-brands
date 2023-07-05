const { execSync } = require('child_process');
const { pluginFiles } = require('./helpers/functions');

// Define the exclude list
const excludeFiles = ['node_modules', 'languages', 'vendor', 'tests', 'build'];

// Filtered include files
const includeFiles = pluginFiles.filter((file) => !excludeFiles.includes(file));

// Construct the command
const report = process.argv[2]?.replace('--', '');

let reportType;

switch (report) {
	case 'xml':
		reportType = '--report-xml=./phpcs_error.xml';
		break;
	case 'csv':
		reportType = '--report-csv=./phpcs_error.csv';
		break;
	default:
		reportType = '--report-file=./phpcs_error.txt';
		break;
}

const command = `php .tools/vendor/bin/phpcs --standard=./phpcs.xml --warning-severity=0 ${reportType} --extensions=php ${includeFiles.join(
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
