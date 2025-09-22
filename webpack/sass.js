const webpack = require('webpack');
const autoPrefixer = require("autoprefixer");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const sassConfig = {
    plugins: [
        new webpack.LoaderOptionsPlugin({
            options: {
                postcss: [
                    autoPrefixer()
                ]
            }
        }),
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: '[id].css',
            ignoreOrder: false,
        }),
    ],
    rule: {
        test: /\.scss$/,
        use: [
            MiniCssExtractPlugin.loader,
            {
                loader: 'css-loader',
                options: {
                    importLoaders: 2,
                    sourceMap: true,
                    url: false
                },
            },
            {
                loader: 'postcss-loader',
                options: {
                    sourceMap: true,
                },
            },
            {
                loader: 'sass-loader',
                options: {
                    sourceMap: true,
                },
            },
        ]
    },
};

module.exports = sassConfig;
