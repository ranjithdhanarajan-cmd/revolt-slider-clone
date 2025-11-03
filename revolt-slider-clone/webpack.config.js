const path = require('path');

module.exports = {
    entry: {
        admin: './assets/js/src/admin.js',
        frontend: './assets/js/src/frontend.js',
        block: './assets/js/src/block.js',
    },
    output: {
        path: path.resolve(__dirname, 'assets/dist'),
        filename: '[name].bundle.js',
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ['@babel/preset-env', { targets: { browsers: ['defaults'] } }],
                            '@babel/preset-react',
                        ],
                    },
                },
            },
        ],
    },
    resolve: {
        extensions: ['.js', '.jsx'],
    },
    externals: {
        react: 'React',
        'react-dom': 'ReactDOM',
        '@wordpress/element': 'wp.element',
        '@wordpress/components': 'wp.components',
        '@wordpress/data': 'wp.data',
        '@wordpress/i18n': 'wp.i18n',
    },
};
