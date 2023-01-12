const path = require('path');
const defaultConfig = require('./node_modules/@wordpress/scripts/config/webpack.config');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const isProduction = process.env.NODE_ENV === 'production';
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const globImporter = require('node-sass-glob-importer');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const config = {
	...defaultConfig,
	plugins: [
		/**
		 * Remove previous instance
		 */
		...defaultConfig.plugins.filter(
			(plugin) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new DependencyExtractionWebpackPlugin({
			requestToExternal: (request, external) => {
				const externals = {
					underscore: ['_', '.'],
					backbone: ['window', 'Backbone'],
				};

				return externals[request] || external;
			},
		}),
	],
};

module.exports = [
	//Frontend
	{
		...config,
		entry: {
			index: path.resolve(__dirname, 'src', './frontend/index.js'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, '../build/frontend/js/'),
		},
		optimization: {
			minimize: isProduction,
		},
	},
	{
		...config,
		entry: {
			index: path.resolve(__dirname, 'src', './frontend/style.scss'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, '../build/frontend/css/'),
		},
		module: {
			...config.module,
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
		...config,
		entry: {
			index: path.resolve(__dirname, 'src', './backend/index.js'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, '../build/backend/js/'),
			library: ['tiktok', 'backend'],
			libraryTarget: 'window',
		},
		optimization: {
			minimize: isProduction,
		},
	},
	{
		...config,
		entry: {
			index: path.resolve(__dirname, 'src', './backend/style.scss'),
		},
		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, '../build/backend/css/'),
		},
		module: {
			...config.module,
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
