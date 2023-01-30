const fs = require('fs');
const path = require('path');

/**
 * Copy files source to target.
 * @param {string} source
 * @param {string} target
 */
function copyFileSync(source, target) {
	var targetFile = target;

	// If target is a directory, a new file with the same name will be created
	if (fs.existsSync(target)) {
		if (fs.lstatSync(target).isDirectory()) {
			targetFile = path.join(target, path.basename(source));
		}
	}

	fs.writeFileSync(targetFile, fs.readFileSync(source));
}

/**
 * Copy folder recursive source to target.
 * @param {string} source
 * @param {string} target
 */
function copyFolderRecursiveSync(source, target) {
	var files = [];

	// Check if folder needs to be created or integrated
	var targetFolder = path.join(target, path.basename(source));
	if (!fs.existsSync(targetFolder)) {
		fs.mkdirSync(targetFolder);
	}

	// Copy
	if (fs.lstatSync(source).isDirectory()) {
		files = fs.readdirSync(source);
		files.forEach(function (file) {
			var curSource = path.join(source, file);
			if (fs.lstatSync(curSource).isDirectory()) {
				copyFolderRecursiveSync(curSource, targetFolder);
			} else {
				copyFileSync(curSource, targetFolder);
			}
		});
	}
}

/**
 * Execute:
 *	Add pluginName folder to ignore files
 *	Get plugin name
 *	Delete old pluginName folder
 *	Create new pluginName folder
 *	Put files and folders in pluginName folder
 */

//Add folder to gitignore
fs.readFile('./.gitignore', function (err, data) {
	if (err) throw err;
	if (!(data.indexOf('/' + pluginName + '/') >= 0)) {
		// Add to .gitignore
		fs.appendFileSync('./.gitignore', '/' + pluginName + '/');
	}
});

//Add folder to prettierignore
fs.readFile('./.prettierignore', function (err, data) {
	if (err) throw err;
	if (!(data.indexOf(pluginName) >= 0)) {
		// Add to .prettierignore
		fs.appendFileSync('./.prettierignore', pluginName);
	}
});

//Add folder to eslintignore
fs.readFile('./.eslintignore', function (err, data) {
	if (err) throw err;
	if (!(data.indexOf(pluginName) >= 0)) {
		// Add to .eslintignore
		fs.appendFileSync('./.eslintignore', pluginName);
	}
});

// Get plugin name
const pluginName = process.env.npm_package_name;

//Delete the old plugin folder
fs.rm('./' + pluginName, {recursive: true}, (err) => {
	//Show status
	err ?? console.log('\x1b[32m%s\x1b[0m',`./${pluginName} folder deleted`);
	//Create pluginName folder
	fs.mkdir(pluginName, function () {
		//Show status
		console.log('\x1b[32m%s\x1b[0m',`./${pluginName} folder successfully created`)
		//Put build folder in pluginName folder
		copyFolderRecursiveSync('./build', './' + pluginName);
		//Put lib folder in pluginName folder
		copyFolderRecursiveSync('./lib', './' + pluginName);
		//Put vendor_packages folder in pluginName folder
		copyFolderRecursiveSync('./vendor_packages', './' + pluginName);
		//Put vendor folder in pluginName folder
		copyFolderRecursiveSync('./vendor', './' + pluginName);
		//Put jetpack_vendor folder in pluginName folder
		copyFolderRecursiveSync('./jetpack_vendor', './' + pluginName);
		//Put pluginName.php script into a pluginName folder
		copyFileSync(pluginName+'.php','./'+pluginName);
		//Add templates folder if exists
		fs.access("./templates", function(error) {
			if (!error) {
				//Put templates folder in pluginName folder
				copyFolderRecursiveSync('./templates', './' + pluginName);
			}
		});
		//Add languages folder if exists
		fs.access("./languages", function(error) {
			if (!error) {
				//Put languages folder in pluginName folder
				copyFolderRecursiveSync('./languages', './' + pluginName);
			}
		});
		//Add assets folder if exists
		fs.access("./assets", function(error) {
			if (!error) {
				//Put assets folder in pluginName folder
				copyFolderRecursiveSync('./assets', './' + pluginName);
			}
		});
		//Add uninstall.php file if exists
		fs.access("./uninstall.php", function(error) {
			if (!error) {
				//Put pluginName.php script into a pluginName folder
				copyFileSync('uninstall.php','./'+pluginName);
			}
		});

		//Remove vendor/bin folder
		fs.rm('./'+pluginName+'/vendor/bin',{recursive: true}, (err) => {
			err ?? true;
		});

		/*
		 * If have a wc parameter need to exclude wp-license-client and wp-notice-plugin-required on:
		 * - File: vendor_packages/wp-license-client
		 * - File: vendor_packages/wp-notice-plugin-required
		 * - Row in pluginName.php
		*/
		const myArgs = process.argv.slice(2);
		if('wc' == myArgs[0]){
			//Remove vendor_packages/wp-license-client file
			fs.rm('./'+pluginName+'/vendor_packages/wp-license-client.php',{recursive: true}, (err) => {
				err ?? true;
			});
			//Remove vendor_packages/wp-notice-plugin-required file
			fs.rm('./'+pluginName+'/vendor_packages/wp-notice-plugin-required.php',{recursive: true}, (err) => {
				err ?? true;
			});
			//Read pluginName file
			var filepath = './'+pluginName+'/'+pluginName+'.php';
			var content = fs.readFileSync(filepath, 'utf-8');
			// replace lines to white space
			var newContent = content.replace("require_once __DIR__ . '/vendor_packages/wp-notice-plugin-required.php';", '');
			var finalContent = newContent.replace("require_once __DIR__ . '/vendor_packages/wp-license-client.php';", '');
			fs.writeFileSync(filepath, finalContent, 'utf-8');
		}

	});
});
