const path = require('path');
const defaultConfig = require('./node_modules/@wordpress/scripts/config/webpack.config');
const isProduction = process.env.NODE_ENV === 'production';
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const globImporter = require('node-sass-glob-importer');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = [
	//Frontend
	{
		...defaultConfig,
		entry: {
			index: path.resolve(__dirname, 'packages', './frontend/index.js'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, 'build/frontend/js/'),
		},
		optimization: {
			minimize: isProduction,
		},
		resolve: {
			alias: {},
		},
	},
	{
		...defaultConfig,
		entry: {
			index: path.resolve(__dirname, 'packages', './frontend/style.scss'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, 'build/frontend/css/'),
		},
		module: {
			...defaultConfig.module,
			rules: [
				{
					test: /\.scss$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
						},
						{
							loader: 'sass-loader',
							options: {
								sassOptions: {
									importer: globImporter(),
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin({
				filename: 'style.css',
			}),
		],
	},
	//Backend
	{
		...defaultConfig,
		entry: {
			index: path.resolve(__dirname, 'packages', './backend/index.js'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, 'build/backend/js/'),
			library: ['tiktok', 'backend'],
			libraryTarget: 'window',
		},
		optimization: {
			minimize: isProduction,
		},
	},
	{
		...defaultConfig,
		entry: {
			index: path.resolve(__dirname, 'packages', './backend/style.scss'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, 'build/backend/css/'),
		},
		module: {
			...defaultConfig.module,
			rules: [
				{
					test: /\.scss$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
						},
						{
							loader: 'sass-loader',
							options: {
								sassOptions: {
									importer: globImporter(),
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin({
				filename: 'style.css',
			}),
		],
	},
];
