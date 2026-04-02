<?php
// HTTP 응답 헤더 설정: 클라이언트에게 JSON 형식으로 응답할 것임을 알림
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'root', 'since1970', 'yurim');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// 1. 요청 메서드 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "POST 요청만 허용됩니다."]);
    exit;
}

// 2. 요청 본문(Raw JSON) 읽기
// JSON 형식의 POST 데이터를 받을 때는 이 방식(php://input)을 사용해야 합니다.
$json_data = file_get_contents('php://input');

// 💡 POST 데이터와 RAW JSON 데이터를 파일에 기록
// POST 배열 내용을 로그에 기록
$log_prefix = sprintf("[%s] POST DATA: ", date('Y-m-d H:i:s'));
//file_put_contents('log.txt', $log_prefix . print_r($_POST, true) . "\n", FILE_APPEND);

// RAW JSON 데이터를 파일에 기록
$log_prefix = sprintf("[%s] RAW DATA: ", date('Y-m-d H:i:s'));
//file_put_contents('log.txt', $log_prefix . $json_data . "\n", FILE_APPEND);
// -----------------------------------------------------

// 3. JSON 데이터를 PHP 연관 배열로 디코딩
$data = json_decode($json_data, true);

// 디코딩 실패 처리 (본문이 비어 있거나 JSON 형식이 잘못된 경우)
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "JSON 데이터 디코딩 오류"]);
    exit;
}

// 4. 데이터가 배열인지 단일 객체인지 확인
// 단일 객체로 왔든, 객체 배열로 왔든 일관된 처리를 위해 배열 형태로 변환합니다.
$is_array = is_array($data) && isset($data[0]);
$data_list = $is_array ? $data : [$data];

// 5. 각 데이터 항목에 대한 유효성 검사 및 처리
$processed_data = [];
$errors = [];

foreach ($data_list as $index => $item) {
    // 필수 데이터 유효성 검사
    if (!isset($item['machine'], $item['data_type'], $item['value'])) {
        $errors[] = "항목 " . ($index + 1) . ": 필수 필드(machine, data_type, value)가 누락되었습니다.";
        continue;
    }
    
    // 데이터 추출 및 처리 (XSS 방지 및 타입 강제 변환)
    $machine = htmlspecialchars($item['machine']);
    $data_type = htmlspecialchars($item['data_type']);
    $value = (float) $item['value'];
    
    // 처리된 데이터 저장
    
    $processed_item = [
        "MACHINE" => $machine,
        "DATA TYPE" => $data_type,
        "VALUE" => $value,
        "TIMESTAMP" => date('Y-m-d H:i:s')
    ];
    
    $processed_data[] = $processed_item;
    
    // 로그 파일에 항목별 데이터 기록
    $log_entry .= sprintf(
        "[%s] Machine: %s, Data Type: %s, Value: %.2f\n",
        $processed_item['TIMESTAMP'],
        $processed_item['MACHINE'],
        $processed_item['DATA TYPE'],
        $processed_item['VALUE']
    );
    //file_put_contents('log.txt', $log_entry, FILE_APPEND);
    $timestamp = date('Y-m-d H:i:s');

    $query = "insert into mes_machine_data (machine, data_type, value, timestamp) values ('{$machine}', '{$data_type}', '{$value}', '{$timestamp}')";
    $result =mysqli_query($conn, $query);
    if(!$result) {
        $response = [
            "status" => "error",
            "message" => "데이터 저장 중 오류가 발생했습니다.",
        ];
        echo json_encode($response);
        //file_put_contents('log.txt', $query . "\n" . mysqli_error($conn) . "\n", FILE_APPEND);
    } else {
        //file_put_contents('log.txt', $query . " success\n", FILE_APPEND);
                // ------------------------------------------------------------------
        // [확률 기반 삭제 로직 추가]
        // 10분의 1 확률로만 삭제 로직 실행 (10초에 1번 꼴)
        if (rand(1, 600) === 1) { 
            
            $one_day_in_seconds = 86400;
            $threshold_time = time() - $one_day_in_seconds; 

            // 24시간보다 오래된 데이터 삭제 (DELETE)
            $delete_query = "DELETE FROM mes_machine_data 
                            WHERE timestamp < {$threshold_time}";
                            
            mysqli_query($conn, $delete_query);
            
        }
        // 10번 중 9번은 이 로직을 건너뛰고 INSERT만 실행
    }
}


// 8. 성공 응답 전송
http_response_code(200); // OK
echo json_encode([
    "status" => "success",
    "message" => "Sensor data has been successfully received and processed.",
    "processed_count" => count($processed_data),
    "received_data" => $processed_data,
]);
?>