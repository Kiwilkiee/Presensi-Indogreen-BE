<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/6.0.0/css/ionicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('asset/css/style.css')}}">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="text-center mb-4">
                <ion-icon name="log-in-outline" style="font-size: 48px; color:black"></ion-icon> <!-- Ikon Login -->
            </div>
            <div class="text-login">
                <h3>Login</h3>
                <p>Silahkan masukkan email dan password Anda </p>
            </div>

            @if( $message = Session::get('error'))
                <div class="alert alert-danger">{{ $message }}</div>
            @endif

            <form method="POST" action="{{ route('prosesLogin') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="Masukan Email" 
                        required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Masukan Password" 
                        required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
