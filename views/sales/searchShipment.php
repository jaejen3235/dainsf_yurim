<div class='main-container'>    
    <div class='content-wrapper'>
        <div class='card-title flex'>
            <div>출하지시서</div>
            <div>
                <input type='text' class='input' name='searchAccount' id='searchAccount' />
                <input type='button' class='btn-mini success' id='btnSearchAccount' value='조회' />
            </div>
        </div>
        <div class='mt10'>
            <table class='shipment-order-list list'>
                <colgroup>                                            
                    <col width='150' />
                    <col width='200' />
                    <col width='300' />
                    <col width='200' />
                    <col width='200' />
                    <col width='150' />
                </colgroup>
                <thead>
                    <tr>
                        <th>출하지시일</th>
                        <th>거래처</th>
                        <th>품목</th>
                        <th>출하지시수량</th>
                        <th>출하상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>

    <div class='content-wrapper mt20'>
        <div class='card-title flex '>
            <div>출하 품목 내역</div>
            <div>
                <input type='text' class='input' name='searchItem' id='searchItem' />
                <input type='button' class='btn-mini success' id='btnSearchItem' value='조회' />
            </div>
        </div>
        <div class='mt10'>
            <table class='delivery-report-list list'>
                <colgroup>                                            
                    <col width='150' />
                    <col width='200' />
                    <col />
                    <col width='300' />
                    <col width='200' />
                    <col width='150' />
                </colgroup>
                <thead>
                    <tr>
                        <th>거래처</th>
                        <th>품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>출하수량</th>
                        <th>출하일자</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>

<?php
include "./views/modal/modalRegisterShipment.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    getShipmentOrderList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getShipmentOrderList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getShipmentOrderList = async ({
    page,
    per = 5,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchAccount');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and name like '%${searchText.value}%'`;
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

        const tableBody = document.querySelector('.shipment-order-list tbody');
        tableBody.innerHTML = generateTableContent1(data);

        getPaging('mes_delivery', 'uid', where, page, per, block, 'getShipmentOrderList');
    } catch (error) {
        console.error('출하지시서 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent1 = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.delivery_date}</td>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name} (${item.item_code})</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='출하내역 보기' onclick='getDeliveryReportList(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getDeliveryReportList = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAllDeliveryReportList');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.delivery-report-list tbody');
        tableBody.innerHTML = generateTableContent3(data);
    } catch (error) {
        console.error('출하 내역 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
}

const generateTableContent3 = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>${item.delivery_date}</td>
        </tr>
    `).join('');
};
</script>