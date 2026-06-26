<?php
error_reporting(E_ALL & ~E_NOTICE);

class RajaOngkirV1 {
    private $apiKey;
    private $baseUrl = 'https://rajaongkir.komerce.id/api/v1/';

    public function __construct() {
        $this->apiKey = 'cJfiWqrFd25af97bd2a2336erJD0iZT5';
    }

    private function callAPI($endpoint, $method = 'GET', $data = []) {
        $curl = curl_init();
        $url = $this->baseUrl . $endpoint;

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->apiKey
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            $options[CURLOPT_HTTPHEADER][] = 'content-type: application/x-www-form-urlencoded';
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return json_encode(['error' => 'cURL Error #: ' . $err]);
        }

        return $response;
    }

    public function searchDestination($searchTerm) {
        $endpoint = 'destination/domestic-destination';
        $data = ['search' => $searchTerm, 'limit' => 50, 'offset' => 0];
        return $this->callAPI($endpoint, 'GET', $data);
    }

    public function calculateCost($originId, $destinationId, $weight, $courier) {
        $endpoint = 'calculate/domestic-cost';
        $data = [
            'origin' => $originId,
            'destination' => $destinationId,
            'weight' => $weight,
            'courier' => $courier
        ];
        return $this->callAPI($endpoint, 'POST', $data);
    }
}

$rajaOngkir = new RajaOngkirV1();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'search_destination':
        header('Content-Type: application/json');
        $searchTerm = $_POST['search_term'] ?? '';
        if (strlen($searchTerm) >= 3) {
            echo $rajaOngkir->searchDestination($searchTerm);
        } else {
            echo json_encode(['data' => []]);
        }
        break;

    case 'cek_ongkir':
        header('Content-Type: text/html');

        $origin_id = $_POST['origin_id'] ?? '';
        $destination_id = $_POST['destination_id'] ?? '';
        $weight = $_POST['weight'] ?? '';
        $courier = $_POST['courier'] ?? '';

        if ($origin_id && $destination_id && $weight && $courier) {
            $response = $rajaOngkir->calculateCost($origin_id, $destination_id, $weight, $courier);
            $resultData = json_decode($response, true);

            if (isset($resultData['meta']['code']) && $resultData['meta']['code'] == 200 && !empty($resultData['data'])) {
                $kurirInfo = strtoupper($resultData['data'][0]['code'] ?? '');

                echo "<h3>Hasil Pengecekan Ongkir ($kurirInfo)</h3>";
                echo "<table class='table shipping-table'>";
                echo "<thead><tr><th>Jenis Layanan</th><th>Deskripsi</th><th>Biaya</th><th>Estimasi</th><th>Aksi</th></tr></thead><tbody>";

                foreach ($resultData['data'] as $cost) {
                    $harga = number_format((int)($cost['cost'] ?? 0), 0, ',', '.');
                    $estimasi = htmlspecialchars($cost['etd'] ?? '-');
                    $service = htmlspecialchars($cost['service'] ?? '-');
                    $description = htmlspecialchars($cost['description'] ?? '-');
                    $costValue = (int)($cost['cost'] ?? 0);

                    echo "<tr>";
                    echo "<td><strong>{$service}</strong></td>";
                    echo "<td>{$description}</td>";
                    echo "<td>Rp {$harga}</td>";
                    echo "<td>{$estimasi}</td>";
                    echo "<td><button type='button' class='btn btn-primary btn-small select-shipping' data-cost='{$costValue}' data-service='{$service}' data-description='{$description}' data-etd='{$estimasi}'>Pilih</button></td>";
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                $errorMessage = $resultData['meta']['message'] ?? 'Tidak dapat menemukan informasi ongkos kirim.';
                echo "<div class='error-message'>Maaf, terjadi kesalahan. Pesan: " . htmlspecialchars($errorMessage) . "</div>";
            }
        } else {
            echo "<div class='error-message'>Parameter tidak lengkap.</div>";
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
