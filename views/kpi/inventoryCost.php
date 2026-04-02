<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card">
                <h4>현재 제품원가</h4>
                <div class="number" id="currentCost">0</div>
                <div class="unit">원</div>
            </div>

            <div class="summary-card">
                <h4>변동</h4>
                <div class="number" id="costChangeRate">0</div>
                <div class="unit">원</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 제품 원가 변동 추이 (Final Cost)</h3>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>      
        </div>

        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 원가 산정 상세 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>산정일자</th>
                        <th>최종 원가</th>
                        <th>변동</th>
                        <th>자재 원가</th>
                        <th>노무비</th>
                        <th>간접비</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="paging-area mt20"></div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 전역 변수 초기화
const sampleData = {
    monthly: []
};

// monthly 데이터 fetch 함수 (mes.php로부터 받아와서 sampleData.monthly에 넣음)
const PRODUCT_ID = '47'; // item_uid를 상수로 정의

async function fetchMonthlyData() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getCostTrendData');
    formData.append('item_uid', PRODUCT_ID); 

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        
        if (data && Array.isArray(data.data)) {
            sampleData.monthly = data.data;
        } else {
            sampleData.monthly = [];
        }
    } catch (error) {
        console.error('fetchMonthlyData error:', error);
        sampleData.monthly = [];
    }
}

// 통계 계산 및 표시 함수 (fetchMonthlyData 결과 기반으로 수정)
function updateSummaryStats(costData) {
    if (costData.length === 0) return;

    // 가장 최근 데이터 (마지막 요소)
    const latestCost = costData[costData.length - 1].final_cost;
    document.getElementById('currentCost').innerHTML = comma(latestCost);
    
    // 변동액 (마지막 데이터 - 이전 데이터)
    if (costData.length >= 2) {
        const previousCost = costData[costData.length - 2].final_cost;
        const delta = latestCost - previousCost;
        let diffDisplay = '';
        let diffDisplayWhite = '';

        if (delta > 0) {
            diffDisplay = `<span style="color: #D23B3B; font-weight:bold;">▲ ${comma(delta)}원</span>`;
            diffDisplayWhite = `<span style="color: #fff;">▲ ${comma(delta)}원</span>`;
        } else if (delta < 0) {
            diffDisplay = `<span style="color: #14833B; font-weight:bold;">▼ ${comma(Math.abs(delta))}원</span>`;
            diffDisplayWhite = `<span style="color: #fff;">▼ ${comma(Math.abs(delta))}원</span>`;
        } else {
            diffDisplay = `<span style="color:#999;">—</span>`;
            diffDisplayWhite = `<span style="color:#fff;">—</span>`;
        }
        document.getElementById('costChangeRate').innerHTML = diffDisplayWhite;
    } else {
        document.getElementById('costChangeRate').innerHTML = `<span style="color:#fff; background-color:#999; font-weight:bold; padding:2px 6px; border-radius:4px;">-</span>`;
    }
}

// 차트 생성 (🚨 핵심 수정 부분)
function createCharts() {
    if (sampleData.monthly.length === 0) {
        console.log("No data to draw the chart.");
        return;
    }
    
    // 1. 차트 데이터 추출
    const labels = sampleData.monthly.map(item => item.date);
    const finalCosts = sampleData.monthly.map(item => item.final_cost);
    const materialCosts = sampleData.monthly.map(item => item.material_cost);
    const laborCosts = sampleData.monthly.map(item => item.labor_cost);
    const indirectCosts = sampleData.monthly.map(item => item.indirect_cost);
    
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    
    new Chart(monthlyCtx, { // monthlyCtx 변수 사용
        type: 'line', 
        data: {
            labels: labels,
            datasets: [
                {
                    label: '최종 원가 (Final Cost)',
                    data: finalCosts,
                    borderColor: '#667eea', // 메인 라인
                    tension: 0.1,
                    fill: false,
                    pointStyle: 'circle',
                    pointRadius: 5,
                    borderWidth: 3
                },
                {
                    label: '총 자재 원가 (Material Cost)',
                    data: materialCosts,
                    borderColor: 'rgba(255, 99, 132, 0.8)', // 자재비 라인
                    tension: 0.1,
                    fill: false,
                    borderDash: [5, 5], // 점선
                    borderWidth: 2
                },
                {
                    // 🚨 노무비와 간접비를 합산하여 표시
                    label: '노무비 + 간접비',
                    data: laborCosts.map((l, i) => l + indirectCosts[i]), 
                    borderColor: 'rgba(54, 162, 235, 0.8)', // 가공비 라인
                    tension: 0.1,
                    fill: false,
                    borderDash: [5, 5],
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false, 
                    title: {
                        display: true,
                        text: '원가 금액 (₩)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: `제품 ID ${PRODUCT_ID}의 원가 변동 추이`
                }
            }
        }
    });
}

// 애니메이션 효과 (기존 로직 유지)
function animateCards() {
    const cards = document.querySelectorAll('.chart-card, .summary-card, .data-table-container');
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

// 상수 정의 (기존 로직 유지)
const CONTROLLER = 'mes';
const MODE = 'getCostTrendData'; 
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

// 상세 테이블 데이터 가져오기 (기존 로직 유지)
const getInventoryCostList = async ({
    page,
    per = 5,
    block = 4,
    item_uid = PRODUCT_ID // 상수를 사용하도록 변경
}) => { 
    // fetchMonthlyData에서 이미 데이터를 가져왔다면 이 함수는 필요하지 않지만, 
    // 별도의 페이징 처리를 위해 그대로 유지합니다.
    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', 'getCostTrendData'); 
    formData.append('item_uid', item_uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        const tableBody = document.querySelector('.list tbody');
        // 데이터 소스를 'data.data'로 맞춤
        tableBody.innerHTML = generateTableContent(data.data); 
    } catch (error) {
        console.error('상세 원가 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

// 테이블 내용 생성 (🚨 핵심 수정 부분: 요약 통계 업데이트 로직 분리)
const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='6'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    // 요약 통계 업데이트는 이 함수가 아닌 updateSummaryStats에서 처리합니다.
    
    return data.map((item, idx, arr) => {
        const prev = idx > 0 ? arr[idx - 1] : null;
        let diffDisplay = `<span style="color:#999;">-</span>`;
        
        if (prev) {
            const delta = (item.final_cost ?? 0) - (prev.final_cost ?? 0); 
            if (delta > 0) {
                diffDisplay = `<span style="color: #D23B3B; font-weight:bold;">▲ ${comma(delta)}</span>`;
            } else if (delta < 0) {
                diffDisplay = `<span style="color: #14833B; font-weight:bold;">▼ ${comma(Math.abs(delta))}</span>`;
            } else {
                diffDisplay = `<span style="color:#999;">—</span>`;
            }
        }
        
        return `
            <tr>
                <td class='center'>${item.date}</td>
                <td class='right'>${comma(item.final_cost)}</td>
                <td class='center'>${diffDisplay}</td>
                <td class='right'>${comma(item.material_cost)}</td>
                <td class='right'>${comma(item.labor_cost)}</td>
                <td class='right'>${comma(item.indirect_cost)}</td>
            </tr>
        `;
    }).join('');
};

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', async function() {
    // 1. 데이터 비동기 로딩
    await fetchMonthlyData();     
    
    // 2. 요약 통계 업데이트 (차트 데이터를 기반으로)
    updateSummaryStats(sampleData.monthly); 
    
    // 3. 차트 생성 (데이터 로딩 완료 후 실행)
    createCharts();
    
    // 4. 상세 목록 생성
    await getInventoryCostList({page:1});
    
    // 5. 애니메이션 적용
    animateCards();

    console.log('초기화 완료. 차트 데이터를 확인하세요:', sampleData.monthly);
});
</script>