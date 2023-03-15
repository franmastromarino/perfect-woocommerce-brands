const fs = require('fs');
const path = require('path');
const { zip, COMPRESSION_LEVEL } = require('zip-a-folder');

const { pluginName, pluginFolder, consoleError, consoleSuccess } = require('./helpers');

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

const _pluginFolder = pluginFolder.substring(0, pluginFolder.lastIndexOf('/'));
			const _pluginFileZipTemp = './.plugin.zip';
			const _pluginFileZip = _pluginFolder + '/' + pluginName + '.zip';
			//Create zip
			compressFromTo(_pluginFolder, _pluginFileZipTemp)
				.then(
					function (err) {
						fs.rename(_pluginFileZipTemp, _pluginFileZip, function (err) {
							//Show status		
							err ?? consoleSuccess(`${_pluginFolder} compressed`)
						})
					},
					function (err) {
						//Show status
						consoleError(`${_pluginFolder} not compressed`)
						consoleError(`${err}`)
					}
				);