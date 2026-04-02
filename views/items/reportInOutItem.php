<div class='main-container'>    
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>                        
                <div class='button-box'>
                </div>
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>
                    <col width='100' />
                    <col />
                    <col width='200' />
                    <col width='200' />
                    <col width='150' />
                    <col width='150' />
                </colgroup>
                <thead>
                    <tr>
                        <th>구분</th>
                        <th>품목명</th>
                        <th>품목코드</th>
                        <th>품목규격</th>
                        <th>입고수량</th>
                        <th>출고수량</th>
                        <th>입/출고 날짜</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>


<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getInOutList({page : 1});
            });
        }
    } catch(e) {}

    // 체크박스
    try{
		const chkAll = document.getElementById('chkAll');
		chkAll.addEventListener('click', ()=>{
			if(chkAll.checked) checkAll('chk');
			else checkAllDisolve('chk');
		});
	} catch(e) {}

    // 모달열기
    try {
        const btnOpenModal = document.getElementById('btnOpenModal');
        if(btnOpenModal) {
            btnOpenModal.addEventListener('click', function() {
                openModal('modalRegisterItem', 900, 500);
            });
        }
    } catch(e) {}

    try {
        const btnRevision = document.getElementById('btnRevision');
        if(btnRevision) {
            btnRevision.addEventListener('click', function() {
                revision();
            });
        }
    } catch(e) {}

    // 선택삭제
    try {
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if(btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteSelected);
        }
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getInOutList({page:1});
            }
        });
    } catch(e) {}

    getInOutList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getItemsInOutList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getInOutList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and itemName like '%${searchText.value}%'`;
            }
        }
    } catch(e) {}
    

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

        getPaging('mes_items_inout', 'uid', where, page, per, block, 'getInOutList');
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.in_qty)}</td>
            <td class='center'>${comma(item.out_qty)}</td>
            <td class='center'>${item.register_date}</td>
        </tr>
    `).join('');
};

const modifyItem = (uid) => {
    getter(uid);
    openModal('modalRegisterItem', 900, 500);
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getInOutList({page:1});
}
</script>