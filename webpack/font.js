const CopyWebpackPlugin = require('copy-webpack-plugin');
const themePath = require('./_theme-path');

const fontConfig = {
    plugins: [
        new CopyWebpackPlugin({
            patterns: [
                {
                    from: `${themePath}/src/fonts/`,
                    to: 'fonts/',
                    toType: 'dir',
                    globOptions: {
                        ignore: ['*.DS_Store', 'Thumbs.db'],
                    },
                },
            ],
        }),
    ],
    rule: {
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        use: [
            {
                loader: 'file-loader',
            },
        ],
    },
};

module.exports = fontConfig;