<?php
// 데이터베이스 연결 정보 (실제 연결 정보로 수정 필요)
$host = 'localhost';
$dbname = 'mbiz';
$username = 'root';
$password = 'since1970';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 기계 ID 배열
    $machines = ['MACHINE001', 'MACHINE002', 'MACHINE003', 'MACHINE004', 'MACHINE005'];
    
    // 각 월별 기본 전력 사용량 범위 (계절성 반영)
    $monthlyBaseValues = [
        1 => ['min' => 280, 'max' => 350],  // 겨울철 높은 사용량
        2 => ['min' => 270, 'max' => 340],
        3 => ['min' => 250, 'max' => 320],
        4 => ['min' => 230, 'max' => 300],
        5 => ['min' => 220, 'max' => 290],
        6 => ['min' => 240, 'max' => 310],  // 여름철 중간 사용량
        7 => ['min' => 260, 'max' => 330],
        8 => ['min' => 270, 'max' => 340],
        9 => ['min' => 250, 'max' => 320],
        10 => ['min' => 230, 'max' => 300],
        11 => ['min' => 250, 'max' => 320],
        12 => ['min' => 270, 'max' => 340]  // 겨울철 높은 사용량
    ];

    // 준비된 statement
    $stmt = $pdo->prepare("INSERT INTO mes_power (machineUid, value, registDate) VALUES (?, ?, ?)");

    // 각 월별로 100개의 데이터 생성
    for ($month = 1; $month <= 12; $month++) {
        // 해당 월의 일수 계산
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2023);
        
        // 100개의 데이터를 해당 월에 고르게 분배
        for ($i = 0; $i < 100; $i++) {
            // 랜덤한 날짜 생성
            $day = rand(1, $daysInMonth);
            $hour = rand(8, 17);  // 업무 시간 (8시 ~ 17시)
            $minute = rand(0, 59);
            $second = rand(0, 59);
            
            $date = sprintf('2023-%02d-%02d %02d:%02d:%02d', $month, $day, $hour, $minute, $second);
            
            // 랜덤한 기계 선택
            $machineUid = $machines[array_rand($machines)];
            
            // 해당 월의 범위 내에서 랜덤한 값 생성
            $baseValue = $monthlyBaseValues[$month];
            $value = round(rand($baseValue['min'] * 10, $baseValue['max'] * 10) / 10, 1);
            
            // 데이터 삽입
            $stmt->execute([$machineUid, $value, $date]);
        }
    }

    echo "샘플 데이터 생성이 완료되었습니다.\n";
    echo "총 " . (12 * 100) . "개의 레코드가 추가되었습니다.\n";

} catch(PDOException $e) {
    echo "에러: " . $e->getMessage();
}
?>