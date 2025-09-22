const jsConfig = {
    rule: {
        test: /\.js$/,
        exclude: /node_modules/,
        use: ['babel-loader'],
    },
};

module.exports = jsConfig;
