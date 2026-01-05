<?php
session_start();
require '../function.php';

if (!isset($_SESSION['loginadmin'])) {
    header("Location: loginadmin.php");
    exit;
}

$admin_name = $_SESSION['useradmin'];
// Handle Teknisi Update
if (isset($_POST['update_teknisi_submit'])) {
    $order_id_teknisi = $_POST['order_id_teknisi'];
    $teknisi = htmlspecialchars($_POST['teknisi']);

    error_log("=== TEKNISI UPDATE DEBUG ===\nOrder ID: $order_id_teknisi\nTeknisi: $teknisi\nPOST Data: " . print_r($_POST, true) . "\n===========================");

    $stmt = $conn->prepare("UPDATE orderperbaikan SET teknisi = ? WHERE id = ?");
    $stmt->bind_param("si", $teknisi, $order_id_teknisi);
    
    if ($stmt->execute()) {
        header("Location: dashboard_admin.php?success=teknisi_updated");
    } else {
        header("Location: dashboard_admin.php?error=update_failed");
    }
    $stmt->close();
    exit;
}

// Handle Delete Order
if (isset($_POST['delete_order_submit'])) {
    $order_id = $_POST['order_id_delete'];
    error_log("=== DELETE ORDER DEBUG ===\nOrder ID: $order_id\nPOST Data: " . print_r($_POST, true) . "\n==========================");
    if (deleteOrder($order_id) > 0) {
        header("Location: dashboard_admin.php?success=order_deleted");
    } else {
        header("Location: dashboard_admin.php?error=delete_failed");
    }
    exit;
}

// Handle Status Update
if (isset($_POST['update_status_submit'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    error_log("=== STATUS UPDATE DEBUG ===\nOrder ID: $order_id\nNew Status: $new_status\nPOST Data: " . print_r($_POST, true) . "\n===========================");
    if (updateStatus($order_id, $new_status) > 0) {
        header("Location: dashboard_admin.php?success=status_updated");
    } else {
        header("Location: dashboard_admin.php?error=update_failed");
    }
    exit;
}

// Handle Catatan Admin Update
if (isset($_POST['update_catatan_submit'])) {
    $order_id_catatan = $_POST['order_id_catatan'];
    $catatan_admin = htmlspecialchars($_POST['catatan_admin']);
    error_log("=== CATATAN UPDATE DEBUG ===\nOrder ID Catatan: $order_id_catatan\nCatatan Admin: $catatan_admin\nPOST Data: " . print_r($_POST, true) . "\n============================");
    if (updateCatatan($order_id_catatan, $catatan_admin) > 0) {
        header("Location: dashboard_admin.php?success=catatan_updated");
    } else {
        header("Location: dashboard_admin.php?error=update_failed");
    }
    exit;
}

$orders = query("SELECT * FROM orderperbaikan ORDER BY STR_TO_DATE(tanggal, '%Y-%m-%d') DESC, id DESC");

// Helper function untuk mendapatkan layanan perbaikan dengan konsisten
function getLayananPerbaikan($order) {
    if (isset($order['layananperbaikan'])) {
        return $order['layananperbaikan'];
    } elseif (isset($order['layananPerbaikan'])) {
        return $order['layananPerbaikan'];
    } elseif (isset($order['layanan_perbaikan'])) {
        return $order['layanan_perbaikan'];
    } else {
        return 'N/A';
    }
}

// Helper function untuk mendapatkan jenis perbaikan dengan konsisten
function getJenisPerbaikan($order) {
    if (isset($order['jenisPerbaikan'])) {
        return $order['jenisPerbaikan'];
    } elseif (isset($order['jenisperbaikan'])) {
        return $order['jenisperbaikan'];
    } elseif (isset($order['jenis_perbaikan'])) {
        return $order['jenis_perbaikan'];
    } else {
        return 'N/A';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rockshoes.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .table-container { overflow-x: auto; }
        textarea { min-height: 60px; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-white fixed w-full z-20 top-0 shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div>
                    <a href="../index.html" class="text-black font-bold text-xl">Rockshoes.id - Admin Panel</a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="dashboard_admin.php" class="text-yellow-500 font-semibold hover:text-yellow-400">Daftar Pesanan</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="font-medium"><i class="fas fa-user-shield mr-1"></i><?= htmlspecialchars($admin_name); ?></span>
                    <a href="logout_admin.php" class="hover:text-red-500" title="Logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="pt-24 pb-16 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-3 text-gray-800">Daftar Pesanan Pelanggan</h2>
            <p class="text-center text-gray-600 mb-8">Kelola pesanan, ubah status, dan berikan catatan untuk setiap pesanan.</p>
            <hr class="mb-8 border-gray-300">

            <?php if(isset($_GET['success'])): ?>
                <div class="mb-4 p-3 rounded-md 
                    <?php if($_GET['success'] == 'status_updated'): echo 'bg-green-100 text-green-700'; endif; ?>
                    <?php if($_GET['success'] == 'catatan_updated'): echo 'bg-blue-100 text-blue-700'; endif; ?>
                    <?php if($_GET['success'] == 'order_deleted'): echo 'bg-red-100 text-red-700'; endif; ?>
                ">
                    <?php 
                        if($_GET['success'] == 'status_updated') echo 'Status pesanan berhasil diperbarui.';
                        if($_GET['success'] == 'catatan_updated') echo 'Catatan admin berhasil disimpan.';
                        if($_GET['success'] == 'order_deleted') echo 'Pesanan berhasil dihapus.';
                    ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="mb-4 p-3 rounded-md bg-red-100 text-red-700">
                    <?php 
                        if($_GET['error'] == 'delete_failed' || $_GET['error'] == 'update_failed') echo 'Gagal memperbarui data. Silakan coba lagi.';
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow-xl rounded-lg p-2 sm:p-6 table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan & Merk</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teknisi</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">Catatan Admin</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                // Mendapatkan data dengan konsisten menggunakan helper functions
                                $layanan = getLayananPerbaikan($order);
                                $merk = $order['merk'] ?? 'N/A';
                                $jenis = getJenisPerbaikan($order);
                                
                                // Debug log dengan data yang sudah diproses
                                error_log("Order ID: " . $order['id'] . 
                                         ", layanan: " . $layanan . 
                                         ", merk: " . $merk . 
                                         ", jenis: " . $jenis);
                                ?>
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($order['id']); ?></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['nama']); ?></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['hp']); ?></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <strong><?= htmlspecialchars($layanan); ?></strong><br>
                                        <span class="text-xs text-gray-500">
                                            <?= htmlspecialchars($merk); ?> - <?= htmlspecialchars($jenis); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?= htmlspecialchars(date('d M Y', strtotime($order['tanggal']))); ?> <br> 
                                        <span class="text-xs text-gray-500"><?= htmlspecialchars($order['waktu']); ?></span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 max-w-xs truncate" title="<?= htmlspecialchars($order['alamat']); ?>"><?= htmlspecialchars($order['alamat']); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <form method="POST" action="dashboard_admin.php" id="teknisi_form_<?= $order['id']; ?>">
                                            <input type="hidden" name="order_id_teknisi" value="<?= $order['id']; ?>">
                                            <input type="text" name="teknisi" value="<?= htmlspecialchars($order['teknisi'] ?? ''); ?>" 
                                                class="w-full border border-gray-300 rounded-md p-1.5 text-xs focus:ring-yellow-500 focus:border-yellow-500" 
                                                placeholder="Nama Teknisi">
                                            <button type="submit" name="update_teknisi_submit" 
                                                class="mt-1 w-full p-1.5 bg-purple-500 text-white rounded-md hover:bg-purple-600 text-xs" 
                                                title="Simpan Teknisi">
                                                <i class="fas fa-user-cog"></i> Simpan Teknisi
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="dashboard_admin.php" class="inline-flex items-center" id="status_form_<?= $order['id']; ?>">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <select name="new_status" id="status_select_<?= $order['id']; ?>" class="p-1 border border-gray-300 rounded-md text-xs focus:ring-yellow-500 focus:border-yellow-500">
                                                <option value="Pembayaranmu Terkonfirmasi" <?= $order['status'] == 'Pembayaranmu Terkonfirmasi' ? 'selected' : ''; ?>>1. Pembayaranmu Terkonfirmasi</option>
                                                <option value="Sepatu Akan Segera Dijemput" <?= $order['status'] == 'Sepatu Akan Segera Dijemput' ? 'selected' : ''; ?>>2. Sepatu Akan Segera Dijemput</option>
                                                <option value="Diproses" <?= $order['status'] == 'Diproses' ? 'selected' : ''; ?>>3. Diproses</option>
                                                <option value="Dalam Penanganan" <?= $order['status'] == 'Dalam Penanganan' ? 'selected' : ''; ?>>4. Dalam Penanganan</option>
                                                <option value="Sepatu Diantar Kembali ke Pelanggan" <?= $order['status'] == 'Sepatu Diantar Kembali ke Pelanggan' ? 'selected' : ''; ?>>5. Sepatu Diantar Kembali ke Pelanggan</option>
                                                <option value="Complete" <?= $order['status'] == 'Complete' ? 'selected' : ''; ?>>6. Complete</option>
                                                <option value="Cancel" <?= $order['status'] == 'Cancel' ? 'selected' : ''; ?>>7. Cancel</option>
                                            </select>
                                            <button type="submit" name="update_status_submit" class="ml-1 p-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs" title="Update Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                        <span class="block mt-1 text-xs font-semibold 
                                            <?php 
                                                if ($order['status'] == 'Complete') echo 'text-green-600';
                                                else if ($order['status'] == 'Cancel') echo 'text-red-600';
                                                else if ($order['status'] == 'Dalam Penanganan' || $order['status'] == 'Diproses') echo 'text-yellow-600';
                                                else if ($order['status'] == 'Sepatu akan Segera Dijemput' || $order['status'] == 'Sepatu Akan Segera Dijemput') echo 'text-yellow-600';
                                                else if ($order['status'] == 'Sepatu Diantar Kembali ke Pelanggan' || $order['status'] == 'Sepatu Diantar Kembali ke Pelanggan') echo 'text-yellow-600';
                                                else echo 'text-gray-600';
                                            ?>
                                        "><?= htmlspecialchars($order['status']); ?></span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <form method="POST" action="dashboard_admin.php" id="catatan_form_<?= $order['id']; ?>">
                                            <input type="hidden" name="order_id_catatan" value="<?= $order['id']; ?>">
                                            <textarea name="catatan_admin" id="catatan_textarea_<?= $order['id']; ?>" class="w-full border border-gray-300 rounded-md p-1.5 text-xs focus:ring-yellow-500 focus:border-yellow-500" placeholder="Belum ada catatan..."><?= htmlspecialchars($order['catatan_admin'] ?? ''); ?></textarea>
                                            <button type="submit" name="update_catatan_submit" class="mt-1 w-full p-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 text-xs" title="Simpan Catatan">
                                                <i class="fas fa-save"></i> Simpan Catatan
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-col space-y-1">
                                            <button onclick="confirmDelete(<?= $order['id']; ?>, '<?= htmlspecialchars($order['nama']); ?>')" class="text-red-600 hover:text-red-900 text-xs text-left" title="Hapus Pesanan">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Hapus Pesanan</h3>
            <p class="mb-4 text-gray-600">Apakah Anda yakin ingin menghapus pesanan dari <span id="customerName" class="font-semibold"></span>?</p>
            <p class="mb-6 text-sm text-red-600"><i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan!</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form method="POST" action="dashboard_admin.php" id="deleteForm" class="inline">
                    <input type="hidden" name="order_id_delete" id="deleteOrderId" value="">
                    <button type="submit" name="delete_order_submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                        <i class="fas fa-trash-alt"></i> Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8 text-center">
        <p>&copy; <?= date("Y"); ?> Rockshoes.id - Admin Panel. All Rights Reserved.</p>
    </footer>

    <script>
    function confirmDelete(orderId, customerName) {
        document.getElementById('deleteOrderId').value = orderId;
        document.getElementById('customerName').textContent = customerName;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
    </script>
</body>
</html>
