const webpack = require('webpack');
const path = require('path');
const package = require('./package.json');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin');

// const config = require( './config.json' );

// Naming and path settings
var appName   = 'app';
var vueVendor = Object.keys(package.dependencies);

vueVendor.splice( vueVendor.indexOf( '@wordpress/hooks' ), 1 );

var entryPoint = {
    'vue-vendor': vueVendor,
};

var exportPath = path.resolve(__dirname, './assets/js');

// Enviroment flag
var plugins = [];
var env = process.env.WEBPACK_ENV;

function isProduction() {
    return process.env.WEBPACK_ENV === 'production';
}

// extract css into its own file
const extractCss = new ExtractTextPlugin({
    filename: "../css/[name].css"
});

plugins.push( extractCss );

// Extract all 3rd party modules into a separate 'vendor' chunk
plugins.push(new webpack.optimize.CommonsChunkPlugin({
    name: 'vue-vendor',
    minChunks: ({ resource }) => /node_modules/.test(resource),
}));

// duplicated CSS from different components can be deduped.
plugins.push(new OptimizeCSSPlugin({
    cssProcessorOptions: {
        safe: true,
        map: {
            inline: false
        }
    }
}));

// Differ settings based on production flag
if ( isProduction() ) {

    plugins.push(new UglifyJsPlugin({
        sourceMap: false,
    }));

    plugins.push(new webpack.DefinePlugin({
        'process.env': env
    }));

    appName = '[name].min.js';
} else {
    appName = '[name].js';
}

module.exports = {
    entry: entryPoint,
    output: {
        path: exportPath,
        filename: appName,
        chunkFilename: 'chunks/[chunkhash].js',
        jsonpFunction: 'wprealizerWebpack'
    },

    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
            '@': path.resolve('./src/'),
        },
        modules: [
            path.resolve('./node_modules'),
            path.resolve(path.join(__dirname, 'src/')),
        ]
    },

    externals: {
        jquery: 'jQuery',
        'chart.js': 'Chart',
        moment: 'moment'
    },

    plugins,

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: require.resolve( 'babel-loader' ),
                        options: {
                            cacheDirectory: process.env.BABEL_CACHE_DIRECTORY || true,
                        },
                    },
                ],
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    extractCSS: true
                }
            },
            {
                test: /\.less$/,
                use: extractCss.extract({
                    use: [{
                        loader: "css-loader"
                    }, {
                        loader: "less-loader"
                    }]
                })
            },
            {
                test: /\.css$/,
                use: [ 'style-loader', 'css-loader' ]
            }
        ]
    },
}
