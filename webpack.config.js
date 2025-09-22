const webpack = require('webpack');
const ESLintPlugin = require('eslint-webpack-plugin');
const path = require('path');
const themePath = require('./webpack/_theme-path');
const jsConfig = require('./webpack/js');
const sassConfig = require('./webpack/sass');
const imageConfig = require('./webpack/image');
const svgConfig = require('./webpack/svg');
const fontConfig = require('./webpack/font');

module.exports = (env) => {
    return {
        plugins: [
            //...(env.development ? [new ESLintPlugin()] : []),
            ...(env.development ? [new webpack.SourceMapDevToolPlugin()] : []),
            ...sassConfig.plugins,
            ...imageConfig.plugins,
            ...svgConfig.plugins,
            ...fontConfig.plugins,
        ],
        entry: {
            scripts: `./${themePath}/src/js/scripts.js`,
            styles: `./${themePath}/src/scss/styles.scss`,
        },
        output: {
            filename: 'js/[name].js',
            path: path.resolve(__dirname, `${themePath}/dist`),
        },
        devtool: env.development ? 'eval-source-map' : false,
        performance: {
            hints: env.development ? 'warning' : false,
        },
        module: {
            rules: [
                jsConfig.rule,
                sassConfig.rule,
                imageConfig.rule,
                fontConfig.rule,
            ],
        },
    };
};
