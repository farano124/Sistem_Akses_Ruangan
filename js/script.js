 // Modal untuk Buat Ruangan
 var modal = document.getElementById("myModal");
 var btn = document.getElementById("myBtn");
 var closeModal = document.getElementsByClassName("close")[0];

 btn.onclick = function() {
     modal.style.display = "block";
 }

 closeModal.onclick = function() {
     modal.style.display = "none";
 }

 window.onclick = function(event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 }

 // Modal untuk Edit Ruangan
var editModal = document.getElementById("editModal");
var editButtons = document.querySelectorAll('.editBtn');

editButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Mengambil data dari atribut button
        document.getElementById('editRuanganId').value = this.getAttribute('data-id');
        document.getElementById('editNamaRuangan').value = this.getAttribute('data-nama');
        document.getElementById('editJenis').value = this.getAttribute('data-jenis');
        document.getElementById('editKapasitas').value = this.getAttribute('data-kapasitas');
        
        // Menampilkan modal
        editModal.style.display = "block";
    });
});

// Menutup modal saat tombol close diklik
var closeEditModal = document.getElementsByClassName("close-edit-schedule")[0];
closeEditModal.onclick = function() {
    editModal.style.display = "none";
}

// Menutup modal saat mengklik di luar modal
window.onclick = function(event) {
    if (event.target == editModal) {
        editModal.style.display = "none";
    }
}

//modal untuk tambah jadwal
var addScheduleModal = document.getElementById("addScheduleModal");
var addScheduleButtons = document.querySelectorAll('.addScheduleBtn');

addScheduleButtons.forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('ruanganId').value = this.getAttribute('data-id');
        document.getElementById('ruanganNama').textContent = this.getAttribute('data-nama');
        addScheduleModal.style.display = "block"; // Tampilkan modal
    });
});

var closeAddScheduleModal = document.getElementsByClassName("close-add-schedule")[0];
closeAddScheduleModal.onclick = function() {
    addScheduleModal.style.display = "none"; // Tutup modal
}

window.onclick = function(event) {
    if (event.target == addScheduleModal) {
        addScheduleModal.style.display = "none"; // Tutup modal jika klik di luar
    }
}

// JavaScript untuk mengelola modal edit
document.querySelectorAll('.editScheduleBtn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const tanggal = button.getAttribute('data-tanggal');
        const jamMulai = button.getAttribute('data-jam_mulai');
        const jamSelesai = button.getAttribute('data-jam_selesai');

        // Mengisi form edit dengan data yang sesuai
        document.getElementById('editJadwalId').value = id;
        document.getElementById('editTanggal').value = tanggal;
        document.getElementById('editJamMulai').value = jamMulai;
        document.getElementById('editJamSelesai').value = jamSelesai;

        // Tampilkan modal
        document.getElementById('editScheduleModal').style.display = 'block';
    });
});

// Menutup modal
document.querySelector('.close-edit-schedule').addEventListener('click', () => {
    document.getElementById('editScheduleModal').style.display = 'none';
});

// Menutup modal jika klik di luar modal
window.onclick = function(event) {
    const modal = document.getElementById('editScheduleModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
