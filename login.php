<?php
include('db.php'); // Menghubungkan dengan file db.php

// Proses Signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    // Menangkap data dari form signup
    $full_name = $_POST['full_name'];
    $nis_nip = $_POST['nis_nip'];
    $role = $_POST['role'];
    $class = $_POST['class'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Meng-hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Memasukkan data ke dalam database
    $sql = "INSERT INTO Users (full_name, nis_nip, role, class, username, email, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$full_name, $nis_nip, $role, $class, $username, $email, $password_hash]);

    echo "Pendaftaran berhasil! Silakan login.";
}

include('db.php'); // Menghubungkan dengan file db.php

//login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Menangkap data dari form login
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Menyaring berdasarkan role yang dipilih

    // Mencari pengguna berdasarkan username dan role
    $sql = "SELECT * FROM Users WHERE username = :username AND role = :role";  // Use named placeholders
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR); // Bind parameters with bindParam
    $stmt->bindParam(':role', $role, PDO::PARAM_STR); // Bind parameters with bindParam
    $stmt->execute();

    // Mengambil hasil query
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah data ditemukan
    if ($result) {
        // Memeriksa apakah password cocok
        if (password_verify($password, $result['password_hash'])) {
            // Jika login berhasil, buat session dan arahkan ke dashboard
            session_start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['role'] = $result['role'];

            // Redirect ke dashboard
            if ($result['role'] == 'Guru') {
                header('Location: dasboardguru.html');
            } elseif ($result['role'] == 'Siswa') {
                header('Location: dashboard-siswa.html');
            } elseif ($result['role'] == 'Tata Usaha') {
                header('Location: dashboard-tatausaha.html');
            }
            exit();
        } else {
            echo "Login gagal. Periksa username dan password Anda.";
        }
    } else {
        echo "Login gagal. Periksa username, password, atau role yang Anda pilih.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auth — Absensi Siswa</title>

    <!-- Bootstrap milikmu -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="bg-light">

    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row shadow-lg rounded-4 overflow-hidden w-100 maxw bg-white">

            <!-- KIRI (branding) -->
            <div class="col-md-6 text-center text-white d-flex flex-column justify-content-center p-5 panel-kiri">
                <h6 class="fw-bold text-uppercase opacity-75 mb-1">Absensi Siswa</h6>
                <h5 class="fw-bold mb-4">SMP GIKI 2 Surabaya</h5>
                <div class="mb-4"><i class="bi bi-journal-text" style="font-size:3rem;"></i></div>
                <p class="opacity-75">Kelola kehadiran siswa, guru, dan tata usaha dengan mudah.</p>
                <div class="d-grid gap-2 px-5 mt-3">
                    <button class="btn btn-light text-primary fw-semibold btn-pill tab-btn" data-target="#loginTab">Login</button>
                    <button class="btn btn-outline-light btn-pill tab-btn" data-target="#signupTab">Sign Up</button>
                </div>
            </div>

            <!-- KANAN (tabs & forms) -->
            <div class="col-md-6 bg-light d-flex flex-column justify-content-center p-5">
                <ul class="nav nav-pills justify-content-center mb-4" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="loginTabBtn" data-bs-toggle="pill"
                            data-bs-target="#loginTab" type="button" role="tab" aria-controls="loginTab"
                            aria-selected="true">
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="signupTabBtn" data-bs-toggle="pill" data-bs-target="#signupTab"
                            type="button" role="tab" aria-controls="signupTab" aria-selected="false">
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    <!-- LOGIN -->
                    <div class="tab-pane fade show active" id="loginTab" role="tabpanel" aria-labelledby="loginTabBtn">
                        <div class="text-center mb-4">
                            <i class="bi bi-house-door" style="font-size:3rem;color:#4f78c8;"></i>
                            <h4 class="fw-bold mt-2 text-primary">LOGIN</h4>
                        </div>

                        <form method="POST">
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="loginUser" name="username" type="text" class="form-control" placeholder="Username" required>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="loginPwd" name="password" type="password" class="form-control" placeholder="Password"
                                    required>
                                <button type="button" class="btn btn-outline-secondary" data-toggle-pwd="#loginPwd">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>

                            <div class="mb-3">
                                <select name="role" id="loginRole" class="form-select" required>
                                    <option selected disabled>Pilih Role</option>
                                    <option value="Siswa">Siswa</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Tata Usaha">Tata Usaha</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <input type="checkbox" id="remember">
                                    <label for="remember" class="ms-1">Remember me</label>
                                </div>
                                <a href="forget-password.html" class="small text-decoration-none text-primary">Lupa
                                    password?</a>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100 btn-pill">Login</button>
                        </form>
                    </div>

                    <!-- SIGN UP -->
                    <div class="tab-pane fade" id="signupTab" role="tabpanel" aria-labelledby="signupTabBtn">
                        <div class="text-center mb-4">
                            <i class="bi bi-house-door" style="font-size:3rem;color:#4f78c8;"></i>
                            <h4 class="fw-bold mt-2 text-primary">SIGN UP</h4>
                        </div>

                        <form method="POST">
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input name="full_name" type="text" class="form-control" placeholder="Nama Lengkap" required>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                                <input name="nis_nip" type="text" class="form-control" placeholder="NIS/NIP" required>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <select name="role" class="form-select" required>
                                    <option selected disabled>Pilih Role Anda</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Siswa">Siswa</option>
                                    <option value="Tata Usaha">Tata Usaha</option>
                                </select>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-columns-gap"></i></span>
                                <input name="class" type="text" class="form-control" placeholder="Kelas (contoh: 9A)">
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input name="username" type="text" class="form-control" placeholder="Username" required>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input name="email" type="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="signupPwd" name="password" type="password" class="form-control" placeholder="Password"
                                    required>
                                <button type="button" class="btn btn-outline-secondary" data-toggle-pwd="#signupPwd">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" name="agree" type="checkbox" id="agree" required>
                                <label class="form-check-label" for="agree">Saya menyetujui syarat & ketentuan.</label>
                            </div>

                            <button type="submit" name="signup" class="btn btn-primary w-100 btn-pill">Sign Up</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Script Bootstrap dan Java.js -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/Java.js"></script>

</body>

</html>
