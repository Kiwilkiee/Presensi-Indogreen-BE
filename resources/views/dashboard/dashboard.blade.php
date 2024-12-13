<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ionicons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/6.0.0/css/ionicons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>
<body>

<div class="container" id="appCapsule">
    <div class="section" id="user-section">
        <div class="d-flex align-items-center">
            <div class="avatar">
                <img src="https://static.vecteezy.com/system/resources/previews/005/544/718/non_2x/profile-icon-design-free-vector.jpg" alt="" class="imaged w64 rounded">
            </div>
            <div class="ms-3">
                <h2 id="user-name">Nama</h2>
                <span id="user-role">Jabatan</span>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-3">
                        <div class="menu-icon">
                            <a href="/profile" class="text-success">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span>Profil</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="menu-icon">
                            <a href="/pengajuan" class="text-danger">
                                <ion-icon name="calendar-number"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span>Cuti</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="menu-icon">
                            <a href="/history" class="text-warning">
                                <ion-icon name="document-text"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span>Histori</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="menu-icon">
                            <a href="{{ route('logout') }}" class="text-orange" >
                                <ion-icon name="exit"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span>Logout</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section mt-2" id="presence-section">

        <div class="row">
            <div class="col-6">
                <button id="absenButtonMasuk" class="card-absensi gradasigreen" onclick="handleAbsenMasuk()" >
                    <div class="card-body text-center ">
                        <div class="iconpresence">
                            <ion-icon name="enter-outline"></ion-icon>
                      </div>
                        <div class="presencedetail">
                            <h4 class="presencetitle">Masuk</h4>
                           
                        </div>
                    </div>
                </button>
            </div>

            <div class="col-6">
                <button id="absenButtonPulang" class="card-absensi gradasired" onclick="handleAbsenPulang()" >
                    <div class="card-body text-center ">
                        <div class="iconpresence">
                            <ion-icon name="exit-outline"></ion-icon>
                        </div>
                        <div class="presencedetail">
                            <h4 class="presencetitle">Pulang</h4>                              
                        </div>
                    </div>
                </button>
            </div>
            <div class="mt-4">
                <h4>Absensi Hari Ini</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-today">
                        <!-- Data absensi akan diisi di sini melalui JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        </div>
     </div>

        <div class="rekappresence mt-3">
            <!-- Contoh konten lain -->
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Ionicons -->
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@6.0.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@6.0.0/dist/ionicons/ionicons.js"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script src="{{ asset('asset/js/absensi.js') }}"></script>

  
      @if( $message = Session::get('success'))
              <Script>
                Swal.fire({
                    title: "berhasil",
                    text: "{{ $message }}",
                    icon: "success"
                    confirmButton: false,

                    });
              </Script>
    @endif

  <script>


    // Contoh JavaScript untuk mengambil data absensi
function fetchTodayAttendance() {
    // Ubah URL ini sesuai dengan API atau sumber data absensi Anda
    const apiUrl = '/api/absensi/today';

    axios.get(apiUrl)
        .then(response => {
            const attendanceData = response.data;
            const attendanceTable = document.getElementById('attendance-today');

            // Kosongkan tabel sebelum diisi
            attendanceTable.innerHTML = '';

            // Isi tabel dengan data absensi
            attendanceData.forEach((attendance) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${attendance.nama}</td>
                    <td>${attendance.jabatan}</td>
                    <td>${attendance.jam_masuk || '-'}</td>
                    <td>${attendance.jam_pulang || '-'}</td>
                `;
                attendanceTable.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching attendance data:', error);
        });
}

// Panggil fungsi untuk mengisi data absensi ketika halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', fetchTodayAttendance);

  </script>


</body>
</html>
