<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary">
                <div class="kpi-card">
                    <h3>현재 검사 상태</h3>
                    <p id="currentInspecting" class="kpi-value">검사중</p>
                </div>
                <div class="kpi-card">
                    <h3>전체 금속 검출 수량</h3>
                    <p id="totalInspected" class="kpi-value">로딩 중...</p>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex">
                    <div class="title red">누전검사 실시간 현황</div>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>설비명</th>
                            <th>데이터 타입</th>
                            <th>값</th>
                            <th>수집일시</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    getLeakageInspection({page:1});
    // 5초마다 자동 갱신
    setInterval(getLeakageInspection({page:1}), 5000);
});



const getLeakageInspection = async ({
    page,
    per = 15,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where machine='Metal_Detect'`;    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getLeakageInspection');
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

        //getPaging('mes_account', 'uid', where, page, per, block, 'getAccountList');
    } catch (error) {
        console.error('거래처 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => {

        document.getElementById('totalInspected').innerText = item.value;

        return `
            <tr>
                <td class='center'>${item.machine}</td>
                <td class='center'>${item.data_type}</td>
                <td class='center'>${item.value}</td>
                <td class='center'>${item.timestamp}</td>
            </tr>
        `;
    }).join('');
};
</script>

