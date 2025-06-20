const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
  mode: 'development',
  entry: {
    'js/app' : './src/js/app.js',
    'js/inicio' : './src/js/inicio.js',
    'js/usuarios/index' : './src/js/usuarios/index.js',
    'js/roles/index' : './src/js/roles/index.js',
    'js/modelos/index' : './src/js/modelos/index.js',
    'js/marcas/index' : './src/js/marcas/index.js',
    'js/clientes/index' : './src/js/clientes/index.js',
    'js/inventario/index' : './src/js/inventario/index.js',
    'js/tipos_servicio/index' : './src/js/tipos_servicio/index.js',
    'js/trabajadores/index' : './src/js/trabajadores/index.js',
    'js/ordenes_reparacion/index' : './src/js/ordenes_reparacion/index.js',
    'js/servicios_orden/index' : './src/js/servicios_orden/index.js',
    'js/ventas/index' : './src/js/ventas/index.js',
    'js/detalle_venta_productos/index' : './src/js/detalle_venta_productos/index.js',
    'js/detalle_venta_servicios/index' : './src/js/detalle_venta_servicios/index.js',
    'js/login/login' : './src/js/login/login.js',
    'js/estadisticas/index' : './src/js/estadisticas/index.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public/build')
  },
  plugins: [
    new MiniCssExtractPlugin({
        filename: 'styles.css'
    })
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: 'asset/resource',
      },
    ]
  }
};