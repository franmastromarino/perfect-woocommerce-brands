const { consoleSuccess, consoleError } = require('./helpers/console');
const { compressFromTo } = require('./helpers/functions');

//Get arguments
// const myArgs = process.argv.slice( 2 );

// //Foreach arguments/folders
// myArgs.forEach( ( element ) => {
// 	compressFromTo( element, `${ element }.zip` ).then(
// 		function ( err ) {
// 			err ?? consoleSuccess( `${ element } compressed` );
// 		},
// 		function ( err ) {
// 			consoleError( `${ err }` );
// 		}
// 	);
// } );

const fs = require('fs');
const path = require('path');
const { promisify } = require('util');

const readdir = promisify(fs.readdir);

const compressPluginDirectories = async () => {
	const pluginDir = './.plugin';

	// Read the contents of the plugin directory
	const files = await readdir(pluginDir);

	// Filter out any files that are not directories
	const directories = files.filter((file) =>
		fs.statSync(path.join(pluginDir, file)).isDirectory()
	);

	// Compress each directory
	for (let dir of directories) {
		const source = path.join(pluginDir, dir);
		const target = `${source}.zip`;

		await compressFromTo(source, target).then(
			() => consoleSuccess(`${source} compressed`),
			(err) => consoleError(`${err}`)
		);
	}
};

compressPluginDirectories();
