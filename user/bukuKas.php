<?php
include '../aksi/koneksi.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id_kas = $_GET['id'];
$user_id = $_SESSION['user_id'];

$res_master = mysqli_query($koneksi, "SELECT * FROM master_kas WHERE id = '$id_kas'");
$master = mysqli_fetch_assoc($res_master);

if (isset($_POST['add_row'])) {
    $tgl = $_POST['tgl'];
    $ket = $_POST['ket'];
    $masuk   = $_POST['masuk'] ?: 0;
    $keluar  = $_POST['keluar'] ?: 0;

    if ($masuk == 0 && $keluar == 0) {
        header("Location: bukuKas.php?id=$id_kas");
        exit();
    }

    if ($masuk > 0) {
        $type = "masuk";
        $jumlah_uang = $masuk;
        $query_update_master = "UPDATE master_kas SET 
                                total_masuk = total_masuk + $jumlah_uang, 
                                user_id = '$user_id' 
                                WHERE id = '$id_kas'";
    } else {
        $type = "keluar";
        $jumlah_uang = $keluar;
        $query_update_master = "UPDATE master_kas SET 
                                total_keluar = total_keluar + $jumlah_uang, 
                                user_id = '$user_id' 
                                WHERE id = '$id_kas'";
    }

    // 1. Simpan detail transaksi ke tabel transaksi_kas
    mysqli_query($koneksi, "INSERT INTO transaksi_kas (id_master, tanggal, keterangan, jumlah_uang, type) 
                            VALUES ('$id_kas', '$tgl', '$ket', '$jumlah_uang', '$type')");

    // 2. Update akumulasi saldo di tabel master_kas
    mysqli_query($koneksi, $query_update_master);

    header("Location: bukuKas.php?id=$id_kas");
    exit();
}

$query = "SELECT * FROM transaksi_kas WHERE id_master = '$id_kas' ORDER BY tanggal ASC";
// Ambil semua data transaksi
$transaksi = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas — <?php echo $master['nama_kas']; ?></title>
    <link rel="stylesheet" href="../style/bukuKas.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <!-- ===== HEADER BAR ===== -->
    <div class="summary-bar">
        <strong><i class="fa-solid fa-book"></i> <?php echo $master['nama_kas']; ?></strong>
        <a href="dashboard.php" class="back-link">Kembali</a>
    </div>

    <!-- ===== TABEL BUKU KAS ===== -->
    <div class="table-wrapper">
        <form method="POST">
        <table class="excel-table">
            <thead>
                <tr>
                    <th class="no-col hide-mobile">No</th>
                    <th style="width:130px">Tanggal</th>
                    <th>Keterangan Transaksi</th>
                    <th style="width:130px">Masuk (Rp)</th>
                    <th style="width:130px">Keluar (Rp)</th>
                    <th style="width:70px">Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1; $total_m = 0; $total_k = 0;
                while ($row = mysqli_fetch_assoc($transaksi)):
                    if ($row['type'] == "masuk") {
                        $total_m += $row['jumlah_uang'];
                    } else {
                        $total_k += $row['jumlah_uang'];
                    }
                ?>
                <tr>
                    <td class="no-col hide-mobile"><?php echo $no++; ?></td>
                    <td><?php echo $row['tanggal']; ?></td>
                    <td><?php echo $row['keterangan']; ?></td>
                    <td class="nominal"><?php if ($row['type'] == "masuk") echo number_format($row['jumlah_uang'], 0, ',', '.'); ?></td>
                    <td class="nominal"><?php if ($row['type'] == "keluar") echo number_format($row['jumlah_uang'], 0, ',', '.'); ?></td>
                    <td align="center">
                    <a class="link-hapus" 
                    href="../aksi/hapusTransaksi.php?id_transaksi=<?php echo $row['id'];?>&back=<?php echo $id_kas;?>" 
                    onclick="konfirmasiHapusTransaksi(event, this.href)">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>

                <!-- Baris Input Baru -->
                <tr class="input-row">
                    <td class="no-col hide-mobile" align="center" style="color:#888">*</td>
                    <td><input type="date" name="tgl" value="<?php echo date('Y-m-d'); ?>" required></td>
                    <td><input type="text" name="ket" placeholder="Ketik keterangan di sini..." required></td>
                    <td><input type="number" name="masuk" placeholder="0" min="0"></td>
                    <td><input type="number" name="keluar" placeholder="0" min="0"></td>
                    <td align="center"><button type="submit" name="add_row" class="btn-save">ADD</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="hide-mobile"></td>
                    <td style="text-align: right" class="label-total">SALDO AKHIR :</td>
                    <td class="nominal">Rp <?php echo number_format($total_m, 0, ',', '.'); ?></td>
                    <td class="nominal">Rp <?php echo number_format($total_k, 0, ',', '.'); ?></td>
                    <td class="saldo-cell">Rp <?php echo number_format($total_m - $total_k, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        </form>
    </div>
    <script>
function konfirmasiHapusTransaksi(event, url) {
    event.preventDefault(); 

    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data transaksi ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
    </script>
</body>
</html>