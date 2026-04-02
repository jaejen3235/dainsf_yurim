<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card">
                <h4>전체 생산량</h4>
                <div class="number" id="totalQty">0</div>
                <div class="unit">개</div>
            </div>

            <div class="summary-card">
                <h4>불량품 수</h4>
                <div class="number" id="totalDefects">0</div>
                <div class="unit">개</div>
            </div>

            <div class="summary-card">
                <h4>불량율</h4>
                <div class="number" id="defectRate">0</div>
                <div class="unit">PPM</div>
            </div>

            <div class="summary-card">
                <h4>양품률</h4>
                <div class="number" id="goodRate">0</div>
                <div class="unit">PPM</div>
            </div>
        </div>

        <div class="charts-grid">
            <!-- 월별 불량율 추이 차트 -->
            <div class="chart-card">
                <h3>📈 월별 불량율 추이</h3>
                <div class="chart-container">
                    <canvas id="monthlyDefectChart"></canvas>
                </div>
            </div>
            <!-- 불량 유형별 분포 차트 -->
            <div class="chart-card">
                <h3>🍕 불량 유형별 분포</h3>
                <div class="chart-container">
                    <canvas id="defectTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 상세 데이터 테이블 -->
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 불량 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>품명</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>불량 유형</th>
                        <th>불량 수량</th>
                        <th>등록 일자</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 샘플 데이터
const sampleData = {
    monthly: [],
    defectTypes: [],
};

// 통계 계산 및 표시
function updateSummaryStats() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefectStat');

    fetch('./handler.php', {
        method: 'post',
        body : formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(function(data) {
        if (data && data.result === 'success') {
            
            // 💡 [가정] 백엔드에서 필요한 값들이 넘어온다고 가정
            const totalQty = Number(data.total_qty) || 0;
            const totalDefects = Number(data.total_defect_qty) || 0;
            const defectRate = Number(data.defect_rate) || 0;
            const goodRate = Number(data.good_rate) || 0;

            const defectPPM = calculatePPM(totalQty, totalDefects);
            const goodQty = parseInt(totalQty) - parseInt(totalDefects);
            const goodPPM = calculatePPM(totalQty, goodQty);
            
            document.getElementById('totalQty').innerHTML = totalQty;
            document.getElementById('totalDefects').innerHTML = totalDefects;
            //document.getElementById('defectRate').innerHTML = defectRate.toFixed(2);
            //document.getElementById('goodRate').innerHTML = goodRate.toFixed(2);
            document.getElementById('defectRate').innerHTML = comma(defectPPM);
            document.getElementById('goodRate').innerHTML = comma(goodPPM);
                
        } else if (data && data.message) {
            console.log(data.message);
        }
    })
    .catch(error => console.log(error)); 
}

// monthly 데이터 fetch 함수 (mes.php로부터 받아와서 sampleData.monthly에 넣음)
async function fetchMonthlyData() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMonthlyDefectStat');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        // data.monthly가 정상적으로 넘어온다고 가정 (예: [{ month: '2024-01', total_quantity: ..., total_orders: ... }, ...])
        if (data && data.result === 'success' && Array.isArray(data.monthly)) {
            sampleData.monthly = data.monthly;
        } else {
            // 실패 케이스 처리
            sampleData.monthly = [];
        }
    } catch (error) {
        console.error('fetchMonthlyData error:', error);
        sampleData.monthly = [];
    }
}

// 불량 유형별 분포 데이터 fetch 함수 (mes.php로부터 받아와서 sampleData.defectTypes에 넣음)
// 파이차트(도넛 차트)에 들어갈 값은 fetch로 받아온 data.defectTypes를 sampleData.defectTypes에 할당하여,
// 파이차트에 전달되는 labels(불량 유형명), datasets.data(불량별 개수/카운트)로 매핑된다.
// 예시: labels: ['이물질', '스크래치', ...], datasets.data: [13, 7, ...]

async function fetchDefectTypeData() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefectTypeStat');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();

        // 예시 데이터: [{type: '이물질', count: 5}, {type: '스크래치', count: 3}]
        // sampleData.defectTypes = data.defectTypes 결과:
        //   (차트 labels에) sampleData.defectTypes.map(item => item.type) : ['이물질', '스크래치']
        //   (차트 data에)   sampleData.defectTypes.map(item => item.count): [5, 3]
        if (data && data.result === 'success' && Array.isArray(data.defectTypes)) {
            sampleData.defectTypes = data.defectTypes;
        } else {
            sampleData.defectTypes = [];
        }
    } catch (error) {
        console.error('fetchDefectTypeData error:', error);
        sampleData.defectTypes = [];
    }
}

// 차트 생성
function createCharts() {
    console.log('Creating charts with data:', sampleData.monthly);
    
    // 월별 불량율 추이 차트
    const monthlyCtx = document.getElementById('monthlyDefectChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: sampleData.monthly.map(item => item.month),
            datasets: [{
                label: '불량율',
                data: sampleData.monthly.map(item => item.defects),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '개';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 불량 유형별 분포 차트
    const typeCtx = document.getElementById('defectTypeChart').getContext('2d');
    
    // 데이터가 있는지 확인
    const hasData = sampleData.defectTypes && sampleData.defectTypes.length > 0 && 
                   sampleData.defectTypes.some(item => item.count > 0);
    
    if (hasData) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: sampleData.defectTypes.map(item => item.type),
                datasets: [{
                    data: sampleData.defectTypes.map(item => item.count),
                    backgroundColor: [
                        '#dc3545', '#fd7e14', '#ffc107', '#20c997', '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        // 데이터가 없을 때 "값이 없습니다" 메시지 표시
        typeCtx.fillStyle = '#6c757d';
        typeCtx.font = '16px Arial';
        typeCtx.textAlign = 'center';
        typeCtx.textBaseline = 'middle';
        typeCtx.fillText('값이 없습니다', typeCtx.canvas.width / 2, typeCtx.canvas.height / 2);
    }
}

// 애니메이션 효과
function animateCards() {
    const cards = document.querySelectorAll('.chart-card, .summary-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getDefectStatDetail';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDefectStatDetail = async ({
    page,
    per = 5,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where qty > 0`;

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_defective_report', 'uid', where, page, per, block, 'getDefectStatDetail');
    } catch (error) {
        console.error('설비 가동률 상세 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.reason}</td>
            <td class='center'>${item.qty}</td>
            <td class='center'>${item.created_dt}</td>
        </tr>
    `).join('');
};

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', async function() {
    updateSummaryStats();
    await fetchMonthlyData();
    await fetchDefectTypeData();
    await getDefectStatDetail({page: 1});
    createCharts();
    animateCards();
});

/**
* 불량품 갯수와 전체 수량을 기반으로 PPM을 계산합니다.
*/
function calculatePPM(total, defects) {
    console.log(total, defects);
    // 숫자 유효성 체크
    if (typeof total !== 'number' || typeof defects !== 'number') return 0;
    if (total <= 0 || defects < 0) return 0;

    const ppm = (defects / total) * 1_000_000;
    return Math.round(ppm);
}

// 사용 예시
//console.log(calculatePPM(4801, 166)); // ≈ 34566 출력

</script>