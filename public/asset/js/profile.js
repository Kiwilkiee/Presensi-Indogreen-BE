document.addEventListener('DOMContentLoaded', function() {
    const user = JSON.parse(localStorage.getItem('user'));

    if (user) {
        document.getElementById('user-name').innerText = user.nama;
        document.getElementById('user-role').innerText = user.jabatan;
        document.getElementById('user-email').innerText = user.email;
        document.getElementById('user-roles').innerText = user.roles;
    }

    // Name edit icon
    document.querySelector('.profile-name .edit-icon').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('editNameModal'));
        document.getElementById('editNameInput').value = user.nama;
        modal.show();
    });

    

    // Email edit icon
    document.querySelector('.profile-email .edit-icon').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('editEmailModal'));
        document.getElementById('editEmailInput').value = user.email;
        modal.show();
    });

    // Password edit icon
    document.querySelector('.profile-password .edit-icon').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('editPasswordModal'));
        modal.show();
    });

    // Submit the forms and update localStorage and backend
    document.getElementById('editNameForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const updatedNama = document.getElementById('editNameInput').value;
        
        axios.put(`/user/${user.id}`, {
            nama: updatedNama,
            email: user.email, // keep the original email
            jabatan: user.jabatan, // keep the original role
        })
        .then(response => {
            const updatedUser = response.data.user;
            localStorage.setItem('user', JSON.stringify(updatedUser));
            document.getElementById('user-name').innerText = updatedUser.nama;
            const modal = bootstrap.Modal.getInstance(document.getElementById('editNameModal'));
            modal.hide();
            Swal.fire({
                title: "Berhasil",
                text: "Nama Kamu Berhasil Diedit!",
                icon: "success"
              });

        })
        .catch(error => {
            Swal.fire({
                title: "Gagal",
                text: "Nama Kamu Gagal Diedit",
                icon: "error"
              });
            console.error('Error updating name:', error);
        });
    });

    

    document.getElementById('editEmailForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const updatedEmail = document.getElementById('editEmailInput').value;

        axios.put(`/user/${user.id}`, {
            nama: user.nama, // keep the original name
            email: updatedEmail,
            jabatan: user.jabatan, // keep the original role
        })
        .then(response => {
            const updatedUser = response.data.user;
            localStorage.setItem('user', JSON.stringify(updatedUser));
            document.getElementById('user-email').innerText = updatedUser.email;
            const modal = bootstrap.Modal.getInstance(document.getElementById('editEmailModal'));
            modal.hide();
            Swal.fire({
                title: "Berhasil",
                text: "Email Kamu Berhasil Diedit",
                icon: "success"
              });
        })
        .catch(error => {
            Swal.fire({
                title: "Gagal",
                text: "Email Kamu Gagal Diedit",
                icon: "error"
              });
            console.error('Error updating email:', error);
        });
    });

    document.getElementById('editPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const updatedPassword = document.getElementById('editPasswordInput').value;

        axios.put(`/user/${user.id}`, {
            nama: user.nama, // keep the original name
            email: user.email, // keep the original email
            jabatan: user.jabatan, // keep the original role
            password: updatedPassword, // only update the password
        })
        .then(response => {
            const updatedUser = response.data.user;
            localStorage.setItem('user', JSON.stringify(updatedUser));
            const modal = bootstrap.Modal.getInstance(document.getElementById('editPasswordModal'));
            modal.hide();
            Swal.fire({
                title: "Berhasil",
                text: "Password Kamu Berhasil Diedit",
                icon: "success"
              });
        })
        .catch(error => {
            Swal.fire({
                title: "Gagal",
                text: "Password Kamu Gagal Diedit",
                icon: "error"
              });
            console.error('Error updating password:', error);
        });
    });
});
