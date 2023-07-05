const fs = require('fs');
const path = require('path');
const archiver = require('archiver');
const { promisify } = require('util');

const mkdir = promisify(fs.mkdir);
const rename = promisify(fs.rename);
const rmdir = promisify(fs.rmdir);
// Get the base directory of the project
const baseDir = process.cwd();

const packageJson = require(path.join(baseDir, 'package.json'));
const pluginName = packageJson.name;
const pluginFolder = './.plugin/' + pluginName;
const pluginFiles = packageJson.scaffolding?.zip?.filter((file) =>
	fs.existsSync(path.join(baseDir, file))
);

const { consoleSuccess } = require('./console');

/**
 * Copy file from source to target.
 *
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
};

const compressFromTo = async (source, target) => {
	const sourcePath = path.resolve(source);
	const targetPath = path.resolve(target);

	// Create a temporary directory
	const tempDir = path.join(path.dirname(sourcePath), 'temp');
	await mkdir(tempDir);

	// Move the source directory into the temporary directory
	const tempSourcePath = path.join(tempDir, path.basename(sourcePath));
	await rename(sourcePath, tempSourcePath);

	// Create a write stream for the zip file
	const output = fs.createWriteStream(targetPath);

	// Create a new archiver instance
	const archive = archiver('zip', {
		zlib: { level: 9 }, // Sets the compression level
	});

	// Pipe the archiver output to the write stream
	archive.pipe(output);

	// Append the temporary directory to the archive
	archive.directory(tempDir, false);

	// Finalize the archive (i.e., finish adding files)
	await archive.finalize();

	// Move the source directory back to its original location
	await rename(tempSourcePath, sourcePath);

	// Delete the temporary directory
	await rmdir(tempDir, { recursive: true });
};

/**
 * Copy folder recursive source to target.
 *
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
};

const cloneDirectory = (source, target) => {
	// Ensure the target directory exists
	if (!fs.existsSync(target)) {
		fs.mkdirSync(target);
	}

	// Get the list of items in the source directory
	const items = fs.readdirSync(source);

	// Copy each item to the target directory
	for (let item of items) {
		const sourcePath = path.join(source, item);
		const targetPath = path.join(target, item);

		// Check if the item is a directory or a file
		const stat = fs.statSync(sourcePath);

		if (stat.isDirectory()) {
			// If the item is a directory, copy it recursively
			cloneDirectory(sourcePath, targetPath);
		} else {
			// If the item is a file, copy it directly
			fs.copyFileSync(sourcePath, targetPath);
		}
	}
};

/**
 * Copy file or folder recursive source to target.
 *
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
};

const moveFromTo = async (source, target) => {
	try {
		const sourcePath = path.resolve(source);
		const targetPath = path.resolve(target);

		if (!fs.existsSync(targetPath)) {
			fs.mkdirSync(targetPath);
		}

		fs.rename(sourcePath, targetPath, function (err) {
			if (err) throw err;
		});
	} catch (ex) {
		console.error('Error moving folder', ex);
	}
};

const deleteThisFrom = async (source, target) => {
	try {
		fs.rm(target + source, { recursive: true }, (err) => {
			err ?? consoleSuccess(`${target + source} deleted`);
		});
	} catch (ex) {
		console.error('Error deleting folder', ex);
	}
};

module.exports.baseDir = baseDir;
module.exports.pluginName = pluginName;
module.exports.pluginFolder = pluginFolder;
module.exports.packageJson = packageJson;
module.exports.pluginFiles = pluginFiles;
module.exports.deleteThisFrom = deleteThisFrom;
module.exports.copyFileFromTo = copyFileFromTo;
module.exports.copyFolderFromTo = copyFolderFromTo;
module.exports.copyFromTo = copyFromTo;
module.exports.moveFromTo = moveFromTo;
module.exports.compressFromTo = compressFromTo;
module.exports.cloneDirectory = cloneDirectory;
