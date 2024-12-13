


document.addEventListener('DOMContentLoaded', function() {
    let userData = localStorage.getItem('user');
    if (userData) {
        let user = JSON.parse(userData);
        document.getElementById('user-name').innerText = user.nama;
        document.getElementById('user-role').innerText = user.jabatan;
    }

    let statusAbsensi = localStorage.getItem('statusAbsensi');
    let today = new Date().toISOString().split('T')[0];

    if (statusAbsensi) {
        statusAbsensi = JSON.parse(statusAbsensi);

        if (statusAbsensi.date === today) {
            if (statusAbsensi.status === 'masuk') {
                        } else if (statusAbsensi.status === 'pulang') {
                    }
        } else {
            localStorage.removeItem('statusAbsensi');
        }
    }
});

function handleAbsenMasuk() {
    let userData = localStorage.getItem('user');
    
    if (!userData) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'User belum login!',
        });
        return;
    }

    let user = JSON.parse(userData);
    let user_id = user.id;
    let now = new Date().toLocaleTimeString();

    // Check if already absen masuk today
    let statusAbsensi = localStorage.getItem('statusAbsensi');
    if (statusAbsensi) {
        statusAbsensi = JSON.parse(statusAbsensi);
        if (statusAbsensi.date === new Date().toISOString().split('T')[0] && statusAbsensi.status === 'masuk') {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Anda sudah absen masuk hari ini!',
            });
            return;
        }
    }

    axios.post('/absensi/masuk', {
        user_id: user_id
    })
    .then(function(response) {
        // document.getElementById('absenButtonMasuk').innerText = 'Sudah Absen Masuk Hari Ini';
        // document.getElementById('masukTime').innerText = 'Waktu Absen Masuk: ' + now;

        let statusAbsensi = {
            status: 'masuk',
            date: new Date().toISOString().split('T')[0],
            jamMasuk: now
        };

        localStorage.setItem('statusAbsensi', JSON.stringify(statusAbsensi));

        // Show success notification
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Anda telah berhasil absen masuk.',
        });
    })
    .catch(function(error) {
        console.log(error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mencoba absen, silakan coba lagi.',
        });
    });
}


function handleAbsenPulang() {
    let userData = localStorage.getItem('user');

    if (!userData) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'User belum login!',
        });
        return;
    }

    let user = JSON.parse(userData);
    let user_id = user.id;
    let now = new Date().toLocaleTimeString();

    // Check if already absen pulang today
    let statusAbsensi = localStorage.getItem('statusAbsensi');
    if (statusAbsensi) {
        statusAbsensi = JSON.parse(statusAbsensi);
        if (statusAbsensi.date === new Date().toISOString().split('T')[0] && statusAbsensi.status === 'pulang') {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Anda sudah absen pulang hari ini!',
            });
            return;
        }
    }

    axios.post('/absensi/pulang', {
        user_id: user_id
    })
    .then(function(response) {
        // document.getElementById('absenButtonPulang').innerText = 'Sudah Absen Pulang Hari Ini';
        // document.getElementById('pulangTime').innerText = 'Waktu Absen Pulang: ' + now;

        let statusAbsensi = {
            status: 'pulang',
            date: new Date().toISOString().split('T')[0],
            jamPulang: now
        };

        localStorage.setItem('statusAbsensi', JSON.stringify(statusAbsensi));

        // Show success notification
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Anda telah berhasil absen pulang.',
        });
    })
    .catch(function(error) {
        console.log(error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mencoba absen, silakan coba lagi.',
        });
    });
}
    

