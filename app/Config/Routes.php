<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Mengarahkan halaman awal website (http://localhost:8080) ke halaman Login
$routes->get('/', 'AuthController::index');
$routes->post('/login-proses', 'AuthController::prosesLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/dashboard', 'DashboardController::index');

// --- RUTE PRODUK ---
$routes->get('/produk', 'ProdukController::index');
$routes->post('/produk/store', 'ProdukController::store');
$routes->post('/produk/update/(:num)', 'ProdukController::update/$1');
$routes->get('/produk/delete/(:num)', 'ProdukController::delete/$1');

// --- RUTE STOK ---
$routes->get('/stok/masuk', 'StokController::masuk');
$routes->post('/stok/masuk/simpan', 'StokController::simpanMasuk');
$routes->get('/stok/keluar', 'StokController::keluar');
$routes->post('/stok/keluar/simpan', 'StokController::simpanKeluar');

// --- RUTE TRANSAKSI ---
$routes->get('/transaksi', 'TransaksiController::index');
$routes->get('/transaksi/buat', 'TransaksiController::buat');
$routes->post('/transaksi/simpan', 'TransaksiController::simpan');
$routes->post('/transaksi/edit/(:num)', 'TransaksiController::edit/$1');
$routes->get('/transaksi/delete/(:num)', 'TransaksiController::delete/$1');
$routes->post('/transaksi/proses-import', 'TransaksiController::prosesImport');

// --- RUTE APRIORI (ANALISIS ASOSIASI) ---
$routes->get('/apriori', 'AprioriController::index');

// --- RUTE IMPORT CSV ---
// Route lama /import dialihkan ke halaman transaksi
$routes->get('/import', function() {
    return redirect()->to('/transaksi');
});
$routes->post('/import/process', 'TransaksiController::prosesImport');

// Kode ini ngapain: Mendaftarkan URL khusus untuk memicu fungsi download Excel
$routes->get('/apriori/export', 'AprioriController::exportExcel');