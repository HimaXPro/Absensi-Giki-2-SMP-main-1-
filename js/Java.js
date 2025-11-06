//  ---------------fungtion Login-------------------------
 // Tombol di panel kiri untuk pindah tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-target');
                const triggerEl = document.querySelector(`[data-bs-target="${target}"]`);
                if (triggerEl) {
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            });
        });

        // Toggle show/hide password (untuk banyak input)
        document.querySelectorAll('[data-toggle-pwd]').forEach(btn => {
            btn.addEventListener('click', () => {
                const selector = btn.getAttribute('data-toggle-pwd');
                const input = document.querySelector(selector);
                if (!input) return;
                const icon = btn.querySelector('i');
                input.type = input.type === 'password' ? 'text' : 'password';
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
    
  
        // --- LOGIN: admin/admin123; route by role ---
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const u = document.getElementById('loginUser').value.trim();
            const p = document.getElementById('loginPwd').value;
            const r = document.getElementById('loginRole').value;

            if (r === 'Pilih Role' || !r) {
                alert('Pilih role terlebih dahulu.');
                return;
            }

            const ok = (u === 'admin' && p === 'admin123');
            if (!ok) {
                alert('Username atau password salah.');
                return;
            }

            // Simpan sesi sesuai role
            localStorage.setItem('loggedIn', 'true');
            localStorage.setItem('role', r);

            if (r === 'Siswa') {
                localStorage.setItem('name', 'Admin');  // contoh
                localStorage.setItem('nis', '120242489');
                window.location.href = 'dashboard-siswa.html';
            } else if (r === 'Guru') {
                localStorage.setItem('name', 'Salsa Inayah');   // contoh
                localStorage.setItem('nip', '12024578');
                window.location.href = 'dasboardguru.html';
            }
            else if (r === 'Tata Usaha') {
                localStorage.setItem('name', 'Rina Marlina');    // contoh
                localStorage.setItem('nip', '12024678');
                window.location.href = 'dashboard-tatausaha.html';
            }
        });
        //-------------------------------------------------------------------------------------