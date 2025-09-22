const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');
const themePath = require('./_theme-path');

const svgConfig = {
    plugins: [
        new SVGSpritemapPlugin(`${themePath}/src/svg/**/*.svg`, {
            output: {
                filename: 'php/sprites.php',
                svgo: true
            },
            sprite: {
                prefix: 'icon-'
            }
        })
    ]
};

module.exports = svgConfig;
