<?php

namespace App\Controllers;

use App\Models\ProdukModel;

class ProdukController extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        // Menginisialisasi ProdukModel agar bisa dipanggil di semua fungsi di bawahnya.
        // Pendekatan ini lebih rapi daripada harus memanggil 'new ProdukModel()' berulang kali di setiap fungsi.
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        // Mengambil seluruh data produk dari database dan mengirimnya ke halaman View (tampilan).
        $data = [
            'title'  => 'Manajemen Produk',
            'produk' => $this->produkModel->findAll()
        ];
        
        return view('produk/index', $data);
    }

    public function store()
    {
        // Menangkap data yang dikirim dari form "Tambah Produk" lalu menyimpannya ke database.
        $this->produkModel->save([
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga'       => $this->request->getPost('harga'),
            'stok'        => 0 // Kenapa 0? Karena penambahan stok akan diatur khusus lewat fitur "Stok Masuk" nantinya agar tercatat historinya.
        ]);
        
        // Setelah berhasil menyimpan, kembalikan user ke halaman produk dengan membawa pesan sukses.
        return redirect()->to('/produk')->with('pesan', 'Data produk berhasil ditambahkan.');
    }

    public function update($id)
    {
        // Menangkap data dari form "Edit Produk" dan mengupdate datanya berdasarkan ID produk tersebut.
        $this->produkModel->save([
            'id'          => $id,
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga'       => $this->request->getPost('harga')
        ]);

        return redirect()->to('/produk')->with('pesan', 'Data produk berhasil diubah.');
    }

    public function delete($id)
    {
        // Menghapus produk dari database berdasarkan ID yang dipilih.
        $this->produkModel->delete($id);
        
        return redirect()->to('/produk')->with('pesan', 'Data produk berhasil dihapus.');
    }
}