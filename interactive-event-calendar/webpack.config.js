const path = require('path');

module.exports = {
    entry: './public/scripts/main.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'public/scripts'),
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react'],
                    },
                },
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
    devServer: {
        contentBase: path.join(__dirname, 'public'),
        compress: true,
        port: 9000,
    },
    mode: 'development',
};