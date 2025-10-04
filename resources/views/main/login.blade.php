<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <title>Pacific Pool - Login Page</title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="76x76" href="https://via.placeholder.com/76x76.png">
  <link rel="icon" type="image/png" href="https://via.placeholder.com/32.png">

  <!-- Boxicons (digunakan untuk ikon di input box) -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- CSS kamu -->
  @vite('resources/css/login.css')
</head>

<body>
  <div class="wrapper">
    <form action="" method="post" role="form text-left">
      @csrf
      <h1>Selamat Datang!</h1>
      <h2>Silahkan melakukan login untuk mengakses.</h2>
      
      <div class="input-box">
        <i class='bx bxs-envelope'></i>
        <input type="text" name="username" placeholder="Masukkan Username" required>
      </div>

      <div class="input-box">
        <i class='bx bxs-lock-alt'></i>
        <input type="password" name="password" placeholder="Masukkan Password" required>
      </div>

      <button type="submit" class="btn btn-primary">Masuk</button>
    </form>
  </div>

  <!-- Tidak perlu JS tambahan kalau hanya login sederhana -->
</body>
</html>
