<div class='main-container'>
    <div class='content-wrapper'>
        <!-- 검색 필터 영역 -->
        <div class="filter-section">
            <div class="filter-box">
                <form id="searchForm" method="GET">
                    <div class="filter-row">
                        <div class="filter-item">
                            <label>검사일자</label>
                            <div class="date-range">
                                <input type="date" id="startDate" name="startDate" class="form-control" value="<?php echo isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-01'); ?>">
                                <span>~</span>
                                <input type="date" id="endDate" name="endDate" class="form-control" value="<?php echo isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="filter-item">
                            <label>제품명</label>
                            <input type="text" id="productName" name="productName" class="form-control" placeholder="제품명 검색" value="<?php echo isset($_GET['productName']) ? $_GET['productName'] : ''; ?>">
                        </div>
                        <div class="filter-item">
                            <label>검사결과</label>
                            <select id="inspectionResult" name="inspectionResult" class="form-control">
                                <option value="">전체</option>
                                <option value="pass" <?php echo (isset($_GET['inspectionResult']) && $_GET['inspectionResult'] == 'pass') ? 'selected' : ''; ?>>양품</option>
                                <option value="fail" <?php echo (isset($_GET['inspectionResult']) && $_GET['inspectionResult'] == 'fail') ? 'selected' : ''; ?>>불량</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>작업라인</label>
                            <select id="workLine" name="workLine" class="form-control">
                                <option value="">전체</option>
                                <option value="line1" <?php echo (isset($_GET['workLine']) && $_GET['workLine'] == 'line1') ? 'selected' : ''; ?>>라인1</option>
                                <option value="line2" <?php echo (isset($_GET['workLine']) && $_GET['workLine'] == 'line2') ? 'selected' : ''; ?>>라인2</option>
                                <option value="line3" <?php echo (isset($_GET['workLine']) && $_GET['workLine'] == 'line3') ? 'selected' : ''; ?>>라인3</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">조회</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilter()">초기화</button>
                        <button type="button" class="btn btn-success" onclick="exportExcel()">엑셀 다운로드</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 통계 카드 영역 -->
        <div class="summary-stats">
            <div class="summary-card">
                <h4>전체 검사 수</h4>
                <div class="number" id="totalInspection">0</div>
                <div class="unit">건</div>
            </div>
            <div class="summary-card">
                <h4>양품 수</h4>
                <div class="number" id="passCount">0</div>
                <div class="unit">건</div>
            </div>
            <div class="summary-card">
                <h4>불량 수</h4>
                <div class="number" id="failCount">0</div>
                <div class="unit">건</div>
            </div>
            <div class="summary-card">
                <h4>불량률</h4>
                <div class="number" id="defectRate">0.00</div>
                <div class="unit">%</div>
            </div>
        </div>

        <!-- 차트 영역 -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📊 불량률 추이</h3>
                <div class="chart-container">
                    <canvas id="defectRateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 검사 현황 테이블 -->
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 검사 현황 목록</h3>
            <table class="list" id="inspectionTable">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>검사일시</th>
                        <th>제품명</th>
                        <th>제품코드</th>
                        <th>작업라인</th>
                        <th>검사결과</th>
                        <th>검출금속</th>
                        <th>검출크기(mm)</th>
                        <th>검사자</th>
                        <th>비고</th>
                    </tr>
                </thead>
                <tbody id="inspectionTableBody">
                    <!-- 데이터가 동적으로 로드됩니다 -->
                    <tr>
                        <td colspan="10" class="center">조회된 데이터가 없습니다.</td>
                    </tr>
                </tbody>
            </table>
            <div class="paging-area mt20" id="paginationWrapper">
                <!-- 페이징이 동적으로 생성됩니다 -->
            </div>
        </div>
    </div>
</div>

<style>
/* 검색 필터 영역 */
.filter-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.filter-box {
    width: 100%;
}

.filter-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.filter-item {
    flex: 1;
    min-width: 200px;
}

.filter-item label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.date-range {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-range span {
    color: #666;
}

.filter-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

/* 통계 카드 영역 - readTime.php 스타일 적용 */
.summary-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.summary-card {
    flex: 1;
    min-width: 200px;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.summary-card h4 {
    font-size: 14px;
    color: #666;
    margin: 0 0 10px 0;
    font-weight: 500;
}

.summary-card .number {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
}

.summary-card .unit {
    font-size: 14px;
    color: #999;
    margin-top: 5px;
}

/* 차트 영역 */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.chart-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chart-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 20px 0;
}

.chart-container {
    position: relative;
    height: 300px;
}

/* 테이블 영역 */
.data-table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.data-table-container h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 20px 0;
}

/* 검사결과 스타일 */
.result-pass {
    color: #28a745;
    font-weight: 600;
}

.result-fail {
    color: #dc3545;
    font-weight: 600;
}

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
    }
    
    .filter-item {
        min-width: 100%;
    }
    
    .summary-stats {
        flex-direction: column;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// 검색 필터 초기화
function resetFilter() {
    document.getElementById('startDate').value = '<?php echo date('Y-m-01'); ?>';
    document.getElementById('endDate').value = '<?php echo date('Y-m-d'); ?>';
    document.getElementById('productName').value = '';
    document.getElementById('inspectionResult').value = '';
    document.getElementById('workLine').value = '';
}

// 엑셀 다운로드
function exportExcel() {
    // 엑셀 다운로드 로직 구현
    alert('엑셀 다운로드 기능은 구현 예정입니다.');
}

// 데이터 로드 함수
function loadInspectionData() {
    // AJAX를 통해 데이터를 로드하는 로직
    // 실제 구현 시 서버 API를 호출하여 데이터를 가져옵니다
    
    // 예시 데이터
    const sampleData = [];
    
    // 통계 업데이트
    updateStatistics(sampleData);
    
    // 테이블 업데이트
    updateTable(sampleData);
}

// 통계 업데이트
function updateStatistics(data) {
    const total = data.length;
    const pass = data.filter(item => item.result === 'pass').length;
    const fail = data.filter(item => item.result === 'fail').length;
    const defectRate = total > 0 ? ((fail / total) * 100).toFixed(2) : 0;
    
    document.getElementById('totalInspection').textContent = total.toLocaleString();
    document.getElementById('passCount').textContent = pass.toLocaleString();
    document.getElementById('failCount').textContent = fail.toLocaleString();
    document.getElementById('defectRate').textContent = defectRate;
    document.getElementById('recordCount').textContent = total.toLocaleString();
}

// 테이블 업데이트
function updateTable(data) {
    const tbody = document.getElementById('inspectionTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="center">조회된 데이터가 없습니다.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map((item, index) => `
        <tr>
            <td class="center">${index + 1}</td>
            <td class="center">${item.inspectionDate || '-'}</td>
            <td class="center">${item.productName || '-'}</td>
            <td class="center">${item.productCode || '-'}</td>
            <td class="center">${item.workLine || '-'}</td>
            <td class="center"><span class="${item.result === 'pass' ? 'result-pass' : 'result-fail'}">${item.result === 'pass' ? '양품' : '불량'}</span></td>
            <td class="center">${item.detectedMetal || '-'}</td>
            <td class="center">${item.detectedSize || '-'}</td>
            <td class="center">${item.inspector || '-'}</td>
            <td class="center">${item.remark || '-'}</td>
        </tr>
    `).join('');
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    // 초기 데이터 로드
    loadInspectionData();
    
    // 차트 초기화 (Chart.js 사용 시)
    // initializeChart();
});

// 차트 초기화 (Chart.js 사용 예시)
function initializeChart() {
    const ctx = document.getElementById('defectRateChart');
    if (ctx) {
        // Chart.js를 사용한 차트 구현
        // new Chart(ctx, { ... });
    }
}
</script>

