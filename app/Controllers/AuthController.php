<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function index()
    {
        // Menampilkan halaman login.
        return view('auth/login');
    }

    public function prosesLogin()
    {
        // Mengecek apakah username dan password cocok.
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Kita hardcode username: admin, password: admin123
        if ($username === 'admin' && $password === 'admin123') {
            
            // Jika benar, simpan status login ke Session (memori sementara browser)
            session()->set([
                'isLoggedIn' => true,
                'username'   => $username
            ]);
            
            // Arahkan ke halaman Dashboard produk
            return redirect()->to('/dashboard');
        }

        // Jika salah, kembalikan ke halaman login dengan pesan error
        return redirect()->to('/')->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        // Menghapus sesi login dan mengembalikan user ke halaman login.
        session()->destroy();
        return redirect()->to('/');
    }
}