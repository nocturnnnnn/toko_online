<?php
session_start();
include "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$query_keranjang = mysqli_query($conn, "
    SELECT k.produk_id, k.jumlah, p.nama_produk, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = '$user_id'
");

if (mysqli_num_rows($query_keranjang) == 0) {
    header("Location: keranjang.php");
    exit;
}

$total_harga = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_keranjang)) {
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_harga += $subtotal;
    $row['subtotal'] = $subtotal;
    $items[] = $row;
}

$shipping_cost = 0;
$shipping_label = '-';
$pesanan_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $shipping_cost = (int)($_POST['shipping_cost'] ?? 0);
    $shipping_label = trim($_POST['shipping_service'] ?? '-');
    $grand_total = $total_harga + $shipping_cost;

    mysqli_query($conn, "INSERT INTO pesanan(user_id, total_harga, status) VALUES('$user_id', '$grand_total', 'pending')");
    $pesanan_id = mysqli_insert_id($conn);

    foreach ($items as $item) {
        $produk_id = $item['produk_id'];
        $jumlah = $item['jumlah'];
        $harga = $item['harga'];
        $subtotal = $item['subtotal'];

        mysqli_query($conn, "INSERT INTO detail_pesanan(pesanan_id, produk_id, jumlah, harga, subtotal) VALUES('$pesanan_id', '$produk_id', '$jumlah', '$harga', '$subtotal')");
        mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id = '$produk_id'");
    }

    mysqli_query($conn, "DELETE FROM keranjang WHERE user_id = '$user_id'");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Berhasil - Nocturn Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="nav-container">
            <a href="../index.php" class="brand">Nocturn Shop</a>
        </div>
    </header>

    <main class="container">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])): ?>
            <div style="text-align: center; padding: 5rem 2rem; background: var(--surface); border-radius: 1rem; border: 1px solid var(--border); max-width: 700px; margin: 4rem auto;">
                <div style="width: 80px; height: 80px; background: #D1FAE5; color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem;">
                    ✓
                </div>
                <h1 style="margin-bottom: 1rem; color: var(--text-primary);">Checkout Berhasil!</h1>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.125rem;">
                    ID Pesanan Anda: <strong>#<?= $pesanan_id; ?></strong>
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.125rem;">
                    Layanan: <strong style="color: var(--primary);"><?= htmlspecialchars($shipping_label); ?></strong>
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.125rem;">
                    Ongkir: <strong style="color: var(--primary);">Rp <?= number_format($shipping_cost, 0, ',', '.'); ?></strong>
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.125rem;">
                    Terima kasih telah berbelanja. Total pembayaran Anda adalah <strong style="color: var(--primary);">Rp <?= number_format($total_harga + $shipping_cost, 0, ',', '.'); ?></strong>.
                </p>
                <a href="../index.php" class="btn btn-primary">Kembali ke Beranda</a>
            </div>
        <?php else: ?>
            <div class="checkout-layout">
                <section class="card-section">
                    <h2 style="margin-bottom: 1rem;">Ringkasan Belanja</h2>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                                        <td><?= $item['jumlah']; ?></td>
                                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card-section">
                    <h2 style="margin-bottom: 1rem;">Cek Ongkir</h2>
                    <form id="form-ongkir">
                        <div class="form-group autocomplete-container">
                            <label for="asal_text">Kota/Kecamatan Asal</label>
                            <input type="text" id="asal_text" class="form-control" placeholder="Contoh: Sinduharjo, Sleman" autocomplete="off" required>
                            <input type="hidden" id="asal_id">
                            <div class="suggestions-list" id="asal_suggestions"></div>
                        </div>

                        <div class="form-group autocomplete-container">
                            <label for="tujuan_text">Kota/Kecamatan Tujuan</label>
                            <input type="text" id="tujuan_text" class="form-control" placeholder="Contoh: Gambir, Jakarta Pusat" autocomplete="off" required>
                            <input type="hidden" id="tujuan_id" name="destination_id">
                            <div class="suggestions-list" id="tujuan_suggestions"></div>
                        </div>

                        <div class="form-group">
                            <label for="berat">Berat (gram)</label>
                            <input type="number" id="berat" name="weight" class="form-control" min="1" value="1000" required>
                        </div>

                        <div class="form-group">
                            <label for="kurir">Pilih Kurir</label>
                            <select id="kurir" name="courier" class="form-control" required>
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>

                        <button type="submit" id="btn-cek" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">Cek Ongkir</button>
                    </form>

                    <div id="hasil_ongkir" style="margin-top: 1rem;"></div>

                    <form id="place-order-form" method="post" action="checkout.php" style="display: none; margin-top: 1rem;">
                        <input type="hidden" name="place_order" value="1">
                        <input type="hidden" name="shipping_cost" id="shipping_cost_form" value="0">
                        <input type="hidden" name="shipping_service" id="shipping_service_form" value="">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Buat Pesanan</button>
                    </form>

                    <div class="cart-summary" style="display: block; margin-top: 1rem;">
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Subtotal Belanja:</span>
                            <div class="total-amount">Rp <?= number_format($total_harga, 0, ',', '.'); ?></div>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Ongkir:</span>
                            <div class="total-amount" id="shipping_amount">Rp 0</div>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary);">Total Bayar:</span>
                            <div class="total-amount" id="grand_total">Rp <?= number_format($total_harga, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        let searchTimeout;

        function handleSearch(inputElement, suggestionsElement, idElement) {
            const searchTerm = $(inputElement).val();
            clearTimeout(searchTimeout);

            if (searchTerm.length < 3) {
                $(suggestionsElement).hide().empty();
                return;
            }

            searchTimeout = setTimeout(function () {
                $.ajax({
                    url: '../api_handler.php',
                    method: 'POST',
                    data: { action: 'search_destination', search_term: searchTerm },
                    beforeSend: function () {
                        $(suggestionsElement).show().html('<li>Mencari...</li>');
                    },
                    success: function (response) {
                        $(suggestionsElement).empty();
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                const suggestionText = `${item.subdistrict_name}, ${item.city_name}, ${item.province_name}`;
                                const suggestionItem = $(`<li data-id="${item.id}">${suggestionText}</li>`);
                                suggestionItem.on('click', function () {
                                    $(inputElement).val($(this).text());
                                    $(idElement).val($(this).data('id'));
                                    $(suggestionsElement).hide().empty();
                                });
                                $(suggestionsElement).append(suggestionItem);
                            });
                        } else {
                            $(suggestionsElement).html('<li>Tidak ditemukan.</li>');
                        }
                    }
                });
            }, 300);
        }

        $('#asal_text').on('keyup', function () { handleSearch(this, '#asal_suggestions', '#asal_id'); });
        $('#tujuan_text').on('keyup', function () { handleSearch(this, '#tujuan_suggestions', '#tujuan_id'); });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.autocomplete-container').length) {
                $('.suggestions-list').hide().empty();
            }
        });

        $('#form-ongkir').on('submit', function (e) {
            e.preventDefault();
            const originId = $('#asal_id').val();
            const destinationId = $('#tujuan_id').val();
            const weight = $('#berat').val();
            const courier = $('#kurir').val();
            const hasilDiv = $('#hasil_ongkir');

            if (!originId || !destinationId || !weight || !courier) {
                alert('Harap lengkapi semua isian dengan memilih dari hasil pencarian!');
                return;
            }

            $.ajax({
                url: '../api_handler.php',
                method: 'POST',
                data: {
                    action: 'cek_ongkir',
                    origin_id: originId,
                    destination_id: destinationId,
                    weight: weight,
                    courier: courier
                },
                beforeSend: function () {
                    $('#btn-cek').prop('disabled', true).text('Mengecek...');
                    hasilDiv.html('<p class="loading">Sedang mencari biaya pengiriman...</p>');
                },
                success: function (response) {
                    hasilDiv.html(response);
                    $('.select-shipping').on('click', function () {
                        const cost = $(this).data('cost');
                        const service = $(this).data('service');
                        $('#shipping_cost_form').val(cost);
                        $('#shipping_service_form').val(service);
                        $('#shipping_amount').text('Rp ' + Number(cost).toLocaleString('id-ID'));
                        $('#grand_total').text('Rp ' + (<?= $total_harga; ?> + cost).toLocaleString('id-ID'));
                        $('.select-shipping').removeClass('btn-success').addClass('btn-primary');
                        $(this).removeClass('btn-primary').addClass('btn-success');
                        $(this).text('Terpilih');
                        $('#place-order-form').show();
                    });
                },
                error: function () {
                    hasilDiv.html('<p class="error-message">Terjadi kesalahan. Silakan coba lagi.</p>');
                },
                complete: function () {
                    $('#btn-cek').prop('disabled', false).text('Cek Ongkir');
                }
            });
        });
    });
    </script>

</body>
</html>
