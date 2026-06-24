// script.js - Kantin Ibun Sofi

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Validasi Form Pemesanan
    const orderForm = document.getElementById('modalOrderForm');
    if(orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const nim = document.getElementById('modal_nim').value;
            const nama = document.getElementById('modal_nama_mahasiswa').value;
            const jumlah = document.getElementById('modal_jumlah').value;
            const menu = document.getElementById('modal_id_menu').value;
            
            if(nama.trim() === '') {
                alert('Nama pemesan tidak boleh kosong!');
                e.preventDefault();
                return;
            }
            if(nim.trim() === '') {
                alert('Nomor meja tidak boleh kosong!');
                e.preventDefault();
                return;
            }
            if(menu === '') {
                alert('Silakan pilih menu!');
                e.preventDefault();
                return;
            }
            if(jumlah <= 0) {
                alert('Jumlah pesanan minimal 1!');
                e.preventDefault();
                return;
            }
        });
    }

    // 2. Perhitungan Total Harga Otomatis (Optional: form utama jika masih ada di halaman lain)
    const menuSelect = document.getElementById('id_menu');
    const jumlahInput = document.getElementById('jumlah');
    const totalInput = document.getElementById('total_harga');
    
    if(menuSelect && jumlahInput && totalInput) {
        function calculateTotal() {
            const selectedOption = menuSelect.options[menuSelect.selectedIndex];
            if(selectedOption && selectedOption.value !== "") {
                const harga = selectedOption.getAttribute('data-harga');
                const jumlah = jumlahInput.value;
                const total = harga * jumlah;
                totalInput.value = total;
            } else {
                totalInput.value = '';
            }
        }
        
        menuSelect.addEventListener('change', calculateTotal);
        jumlahInput.addEventListener('input', calculateTotal);
    }

    // 3. Filter dan Pencarian Menu
    const searchInput = document.getElementById('searchInput');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuCards = document.querySelectorAll('.menu-card');
    
    if(searchInput) {
        searchInput.addEventListener('input', filterMenu);
    }
    
    if(filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all
                filterBtns.forEach(b => b.classList.remove('btn-primary'));
                filterBtns.forEach(b => b.classList.add('btn-outline'));
                
                // Add active class to clicked
                btn.classList.remove('btn-outline');
                btn.classList.add('btn-primary');
                
                // Store active filter
                document.activeFilter = btn.getAttribute('data-filter');
                filterMenu();
            });
        });
    }
    
    function filterMenu() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const filterCat = document.activeFilter || 'all';
        
        menuCards.forEach(card => {
            const title = card.querySelector('.menu-title').textContent.toLowerCase();
            const cat = card.getAttribute('data-category');
            
            const matchSearch = title.includes(searchTerm);
            const matchFilter = (filterCat === 'all' || cat === filterCat);
            
            if(matchSearch && matchFilter) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Auto remove alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 3000);
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('orderModal');
        if (event.target == modal) {
            closeOrderModal();
        }
    }
});

// Modal Functions
function openOrderModal(id, nama, harga) {
    const modal = document.getElementById('orderModal');
    if (modal) {
        document.getElementById('modal_id_menu').value = id;
        document.getElementById('modalMenuName').innerText = nama;
        document.getElementById('modal_harga').value = harga;
        document.getElementById('modal_jumlah').value = 1;
        
        calculateModalTotal();
        modal.style.display = 'flex';
    }
}

function closeOrderModal() {
    const modal = document.getElementById('orderModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function calculateModalTotal() {
    const harga = document.getElementById('modal_harga').value;
    const jumlah = document.getElementById('modal_jumlah').value;
    const totalInput = document.getElementById('modal_total_harga');
    
    if (harga && jumlah && totalInput) {
        totalInput.value = harga * jumlah;
    }
}
