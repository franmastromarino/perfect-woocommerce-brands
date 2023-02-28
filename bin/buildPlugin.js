const fs = require('fs');
const path = require('path');
const { zip, COMPRESSION_LEVEL } = require('zip-a-folder');

const myArgs = process.argv;

const { pluginName, pluginFolder, copyFromToArr, consoleError, consoleSuccess, deleteFromPluginFolder } = require('./helpers');

const PLUGIN_FILES = [
	{
		source: pluginName + '.php',
		required: true,
	},
	{
		source: "readme.txt",
	},
	{
		source: "changelog.txt",
	},
	{
		source: "uninstall.php",
	},
	{
		source: "wpml-config.xml",
	},
	{
		source: "./lib",
		required: true,
	},
	{
		source: "./vendor",
		required: true,
	},
	{
		source: "./vendor_packages",
		required: true,
	},
	{
		source: "./jetpack_vendor",
		required: true,
	},
	{
		source: "./compatibility"
	},
	{
		source: "./build",
	},
	{
		source: "./templates",
	},
	{
		source: "./languages",
	},
	{
		source: "./assets",
	},

]

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

/**
 * Execute:
 *	Delete old pluginFolder folder
 *	Create new pluginFolder folder
 *	Copy files and folders in pluginFolder folder
 */
fs.rm(pluginFolder, { recursive: true }, (err) => {
	//Show status
	err ?? consoleSuccess(`${pluginFolder} deleted`)
	//Create pluginName folder
	fs.mkdir(pluginFolder, { recursive: true }, (err) => {
		//Show status
		err ?? consoleSuccess(`${pluginFolder} created`);

		//Copy files and folders in pluginName folder
		copyFromToArr(PLUGIN_FILES);

		//Delete files and folders in pluginName folder
		deleteFromPluginFolder('/vendor/bin');

		/*
		 * If have a wc parameter need to exclude wp-license-client and wp-notice-plugin-required on:
		 * - File: vendor_packages/wp-license-client
		 * - File: vendor_packages/wp-notice-plugin-required
		 * - Row in pluginName.php
		*/
		if (myArgs.includes('--wc')) {
			deleteFromPluginFolder('/vendor_packages/wp-license-client.php');
			deleteFromPluginFolder('/vendor_packages/wp-notice-plugin-required.php');
			//Read pluginName file
			const pluginFileMain = pluginFolder + '/' + pluginName + '.php';
			const pluginFileMainContent = fs.readFileSync(pluginFileMain, 'utf-8');
			// Replace lines to white space
			const newContent = pluginFileMainContent.replace("require_once __DIR__ . '/vendor_packages/wp-notice-plugin-required.php';", '');
			const pluginFileMainContentEdited = newContent.replace("require_once __DIR__ . '/vendor_packages/wp-license-client.php';", '');
			fs.writeFileSync(pluginFileMain, pluginFileMainContentEdited, 'utf-8');
		}
		/**
		 * Execute:
		 *	Compress .plugin folder to .plugin.zip
		 *	Create new pluginName zip
		 */
		if (myArgs.includes('--zip')) {
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
		}
	});
});
