const consoleSuccess = (message) => {
	console.log('\x1b[32m%s\x1b[0m', message);
};

const consoleError = (message) => {
	console.log('\x1B[31m', message);
};

const consoleInfo = (message) => {
	console.log('\x1b[36m%s\x1b[0m', message);
};

module.exports.consoleSuccess = consoleSuccess;
module.exports.consoleError = consoleError;
module.exports.consoleInfo = consoleInfo;
