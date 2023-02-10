const fs = require('fs');
const path = require('path');
const { zip, COMPRESSION_LEVEL } = require('zip-a-folder');

const pluginName = process.env.npm_package_name;
const pluginFolder = './.plugin/' + pluginName;

/**
 * Copy file from source to target.
 * @param {string} source
 * @param {string} target
 */
const copyFileFromTo = (source, target) => {
	// If target is a directory, a new file with the same name will be created
	if (fs.existsSync(target)) {
		if (fs.lstatSync(target).isDirectory()) {
			target = path.join(target, path.basename(source));
		}
	}
	fs.writeFileSync(target, fs.readFileSync(source));
}

/**
 * Copy folder recursive source to target.
 * @param {string} source
 * @param {string} target
 */
const copyFolderFromTo = (source, target) => {
	// Check if folder needs to be created or integrated
	const targetFolder = path.join(target, path.basename(source));
	// Create target folder if it doesn't exist
	if (!fs.existsSync(targetFolder)) {
		fs.mkdirSync(targetFolder);
	}
	// Copy folder or folder files
	if (fs.lstatSync(source).isDirectory()) {
		const files = fs.readdirSync(source);
		files.forEach(function (file) {
			const filePath = path.join(source, file);
			if (fs.lstatSync(filePath).isDirectory()) {
				copyFolderFromTo(filePath, targetFolder);
			} else {
				copyFileFromTo(filePath, targetFolder);
			}
		});
	}
}

/**
 * Copy file or folder recursive source to target.
 * @param {string} source
 * @param {string} target
 */
const copyFromTo = (source, target) => {
	// Check if is folder or file
	if (fs.lstatSync(source).isDirectory()) {
		copyFolderFromTo(source, target);
	} else {
		copyFileFromTo(source, target);
	}
}

/**
 * Copy files from source to target.
 * 
 * @param {array} files 
 */
const copyFromToArr = async (files) => {
	for (const file of files) {
		const { source, target = pluginFolder, required = false } = file;
		if (required) {
			copyFromTo(source, target)
		} else {
			fs.access(source, function (error) {
				if (!error) {
					copyFromTo(source, target);
				}
			});
		}
	}
};

/**
 * Compresses a folder to the specified zip file.
 * @param {string} folder 
 * @param {string} filePath 
 */
const compressFromTo = async (source, target) => {
	const sourcePath = path.resolve(source);
	const targetPath = path.resolve(target);
	return await zip(sourcePath, targetPath, { compression: COMPRESSION_LEVEL.high });
};

const moveFromTo = async (source, target) => {
	try {
		const sourcePath = path.resolve(source);
		const targetPath = path.resolve(target);

		if (!fs.existsSync(targetPath)) {
			fs.mkdirSync(targetPath);
		}

		fs.rename(sourcePath, targetPath, function (err) {
			if (err) throw err
		})

	} catch (ex) {
		console.error('Error moving folder', ex);
	}
}

const deleteFromPluginFolder = async (source) => {
	try {
		fs.rm(pluginFolder + source, { recursive: true }, (err) => {
			err ?? consoleSuccess(`${pluginFolder + source} deleted`)
		});
	} catch (ex) {
		console.error('Error deleting folder', ex);
	}
}

const consoleSuccess = (message) => {
	console.log('\x1b[32m%s\x1b[0m', message);
}

const consoleError = (message) => {
	console.log('\x1B[31m', message);
}

const consoleInfo = (message) => {
	console.log('\x1b[36m%s\x1b[0m', message);
}

module.exports.pluginName = pluginName;
module.exports.pluginFolder = pluginFolder;
module.exports.copyFromToArr = copyFromToArr;
module.exports.compressFromTo = compressFromTo;
module.exports.deleteFromPluginFolder = deleteFromPluginFolder;
module.exports.consoleSuccess = consoleSuccess;
module.exports.consoleError = consoleError;
module.exports.consoleInfo = consoleInfo;