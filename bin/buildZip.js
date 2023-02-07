const fs = require('fs');
const fsp = require('fs/promises');
const path = require('path');
const JSZip = require('jszip');

/**
 * Returns a flat list of all files and subfolders for a directory (recursively).
 * @param {string} dir
 * @returns {Promise<string[]>}
 */
const getFilePathsRecursively = async (dir) => {
	// returns a flat array of absolute paths of all files recursively contained in the dir
	const list = await fsp.readdir(dir);
	const statPromises = list.map(async (file) => {
		const fullPath = path.resolve(dir, file);
		const stat = await fsp.stat(fullPath);
		if (stat && stat.isDirectory()) {
			return getFilePathsRecursively(fullPath);
		}
		return fullPath;
	});

	return (await Promise.all(statPromises)).flat(Infinity);
};

/**
 * Creates an in-memory zip stream from a folder in the file system
 * @param {string} dir
 * @returns {JSZip}
 */
const createZipFromFolder = async (dir) => {
	const absRoot = path.resolve(dir);
	const filePaths = await getFilePathsRecursively(dir);
	return filePaths.reduce((z, filePath) => {
		const relative = filePath.replace(absRoot, '');
		// create folder trees manually :(
		const zipFolder = path
			.dirname(relative)
			.split(path.sep)
			.reduce((zf, dirName) => zf.folder(dirName), z);

		zipFolder.file(path.basename(filePath), fs.createReadStream(filePath));
		return z;
	}, new JSZip());
};

/**
 * Compresses a folder to the specified zip file.
 * @param {string} srcDir
 * @param {string} destFile
 */
const compressFolder = async (srcDir, destFile) => {
	const start = Date.now();
	try {
		const zip = await createZipFromFolder(srcDir);
		zip.generateNodeStream({ streamFiles: true, compression: 'DEFLATE' })
			.pipe(fs.createWriteStream(destFile))
			.on('error', (err) =>
				console.error('Error writing file', err.stack)
			)
			.on('finish', () =>{
					//Delete pluginName folder
					deleteFolderRecursive('./' + pluginName);
					console.log('\x1b[32m%s\x1b[0m',`./${pluginName} folder deleted`);
				}
			);
	} catch (ex) {
		console.error('Error creating zip', ex);
	}
};

/**
 * Delete a folder (recursively).
 * @param {string} dir
 */
const deleteFolderRecursive = function (directoryPath) {
    if (fs.existsSync(directoryPath)) {
		fs.readdirSync(directoryPath).forEach((file, index) => {
			const curPath = path.join(directoryPath, file);
			if (fs.lstatSync(curPath).isDirectory()) {
				// recurse
				deleteFolderRecursive(curPath);
			} else {
				// delete file
				fs.unlinkSync(curPath);
			}
		});
		fs.rmdirSync(directoryPath);
	}
};

/**
 * Execute:
 *	Get plugin name
 *	Delete old pluginName zip
 *	Create new pluginName zip
 */

// Get plugin name
const pluginName = process.env.npm_package_name;

// Delete old pluginName zip
fs.rm('./' + pluginName + '.zip', function (err) {
	//Show status
	err ?? console.log('\x1b[32m%s\x1b[0m',`./${pluginName}.zip deleted`);
	//Create zip
	compressFolder('./' + pluginName, './' + pluginName + '.zip').then(function() {
		//Show status
		console.log('\x1b[32m%s\x1b[0m',`${pluginName}.zip successfully created`)
	}, function() {
		//Show status
		console.log('\x1B[31m',`${pluginName}.zip not created`)
	});
});
