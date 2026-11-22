<?php
class AuthController
{
    public static function loginForm(): void
    {
        if (Auth::check()) redirect('/admin');
        view('admin/login', ['title' => 'Masuk', 'error' => null, 'username' => ''], 'auth');
    }

    public static function login(): void
    {
        csrf_check();
        $username = post_str('username', 60);
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $error = Auth::attempt($username, $password);
        if ($error === null) redirect('/admin');
        http_response_code(401);
        view('admin/login', ['title' => 'Masuk', 'error' => $error, 'username' => $username], 'auth');
    }

    public static function logout(): void
    {
        csrf_check();
        Auth::logout();
        redirect('/admin/login');
    }
}
