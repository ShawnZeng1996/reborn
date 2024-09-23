// webpack.config.js
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  entry: './main.js', // 入口文件
  output: {
    filename: 'rb.bundle.js', // 输出文件名
    path: path.resolve(__dirname, 'assets/js'), // 输出文件路径
  },
  //mode: 'production',
  mode: 'development',
  module: {
    rules: [
      {
        test: /\.css$/i, // 匹配所有的 .css 文件
        use: ['style-loader', 'css-loader'], // 使用这两个 loader
      },
    ],
  },
  optimization: {
    minimize: true,
    minimizer: [new TerserPlugin({
      terserOptions: {
        mangle: true, // 混淆变量名和方法名
        compress: {
          drop_console: false, // 移除console.log等
        },
      },
    })],
  },
};