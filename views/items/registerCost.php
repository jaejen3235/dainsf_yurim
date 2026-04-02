<?php
$conn = mysqli_connect('localhost', 'root', 'since1970', 'yurim');
$query = "select * from mes_items where uid = '$_GET[item_uid]'";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

$query = "select * from mes_items";
$result = mysqli_query($conn, $query);
?>
<div class='main-container'>
    <!-- 제품 기본 정보 -->
    <div class="content-wrapper">        
        <form id="frm">
            <input type='hidden' name='controller' id='controller' value='mes' />
            <input type='hidden' name='mode' id='mode' value='registerCost' />
            <input type='hidden' name='item_uid' id='item_uid' value="<?php echo $_GET['item_uid']; ?>" />
            <input type='hidden' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />

            <div class="title red">제품 기본 정보</div>
            <table class='register'>
                <colgroup>
                    <col width='15%'>
                    <col width='35%'>
                    <col width='15%'>
                    <col width='35%'>
                </colgroup>
                <tr>
                    <th>품목</th>
                    <td><?php echo $item['item_name']; ?></td>
                    <th>판매가 (₩)</th>
                    <td><?php echo number_format($item['price']); ?></td>
                </tr>
            </table>

            <div class='flex mt30'>
                <div class="title red">원자재 명세서 (BOM)</div>
                <button type="button" class='btn-small success hands' id="addBomRow">자재 추가</button>
            </div>
            <table class='register mt10' id="bomTable">
                <thead>
                    <tr>
                        <th style="width: 30%;">자재명</th>
                        <th style="width: 20%;">소요량</th>
                        <th style="width: 20%;">단위당 원가 (₩)</th>
                        <th style="width: 20%;">합계 원가 (₩)</th>
                        <th style="width: 10%;">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="bomRowTemplate">
                        <td class='center'>
                            <select class='material_name' name="material_name[]" id="material_name">
                                <?php while($item = mysqli_fetch_assoc($result)) { ?>
                                    <option value="<?php echo $item['uid']; ?>"><?php echo $item['item_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="bom-qty right" name="quantity[]" id="quantity" value="0">
                        </td>
                        <td>
                            <input type="text" class="bom-unit-cost right" name="unit_cost[]" id="unit_cost" value="0">
                        </td>
                        <td>
                            <input type="text" class="bom-total-cost right" name="total_cost[]" id="total_cost" readonly value="0">
                        </td>
                        <td class='center'>
                            <button type='button' class='btn-small danger remove-row'>삭제</button>
                        </td>
                    </tr>
                </tbody>
            </table> 

            <div class="title red mt30">기타 제조 비용</div>
            <table class='register'>
                <tr>
                    <th>직접 노무비 (₩)</th>
                    <td>
                        <input type="text" class='input' name="labor_cost" id="labor_cost" required>
                    </td>
                    <th>제조 간접비/경비 (₩)</th>
                    <td>
                        <input type="text" class='input' name="indirect_cost" id="indirect_cost" required>
                    </td>
                </tr>
            </table>

            <div class="title red mt30">최종 원가 분석</div>
            <table class='register'>
                <tr>
                    <th>총 원자재 원가 (A)</th>
                    <td>
                        <input type="text" class='input' name="total_material_cost" id="total_material_cost" required>
                    </td>
                    <th>총 기타 비용 (B)</th>
                    <td>
                        <input type="text" class='input' name="total_other_cost" id="total_other_cost" required>
                    </td>
                </tr>
                <tr>
                    <th>최종 산출 원가 (A + B)</th>
                    <td colspan="3">
                        <input type="text" class='input' name="final_cost" id="final_cost" required>
                    </td>
                </tr>
            </table>
            <div class="center mt30">
                <button type="button" class='btn-large primary' id="calculateCostBtn">원가 재계산</button>
                <button type="button" class='btn-large success' id="saveCostBtn">원가 정보 저장</button>
            </div>
        </form>
    </div> 
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    

    const templateRow = document.getElementById('bomRowTemplate');
    const addBomRowBtn = document.getElementById('addBomRow');
    const calculateCostBtn = document.getElementById('calculateCostBtn');
    

    // 템플릿 행을 숨김
    templateRow.style.display = 'none';

    // 초기 BOM 행 하나 추가
    addBomRow(); 

    // 이벤트 리스너 연결
    addBomRowBtn.addEventListener('click', addBomRow);
    calculateCostBtn.addEventListener('click', calculateTotalCost);
        
    // 기타 비용 필드 변경 시 자동 계산
    document.getElementById('labor_cost').addEventListener('input', calculateTotalCost);
    document.getElementById('indirect_cost').addEventListener('input', calculateTotalCost);

    // 저장 버튼 (실제 DB 연동 로직은 백엔드에서 구현 필요)
    document.getElementById('saveCostBtn').addEventListener('click', register);
});

// 템플릿 행 복제하여 추가하는 함수
function addBomRow() {
    const bomTableBody = document.querySelector('#bomTable tbody');
    const templateRow = document.getElementById('bomRowTemplate');
    const newRow = templateRow.cloneNode(true);
    newRow.removeAttribute('id');
    newRow.style.display = ''; // 보이게 설정
    newRow.querySelectorAll('input').forEach(input => input.value = (input.type === 'number') ? 0 : '');
            
    // 삭제 버튼 이벤트 리스너 추가
    newRow.querySelector('.remove-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotalCost();
    });

    // 입력 필드 변경 시 자동 계산
    newRow.querySelectorAll('.bom-qty, .bom-unit-cost').forEach(input => {
        input.addEventListener('input', calculateRowTotal);
    });

    bomTableBody.appendChild(newRow);
    calculateRowTotal.call(newRow.querySelector('.bom-qty')); // 초기 계산
}

// BOM 행 합계 계산 함수
function calculateRowTotal() {
    const row = this.closest('tr');
    const qty = parseFloat(row.querySelector('.bom-qty').value) || 0;
    const unitCost = parseFloat(row.querySelector('.bom-unit-cost').value) || 0;
    const totalCostInput = row.querySelector('.bom-total-cost');
            
    const total = qty * unitCost;
    totalCostInput.value = total.toFixed(2);
    calculateTotalCost(); // 행이 변경되면 전체 원가 재계산
}

// 전체 원가 계산 함수
function calculateTotalCost() {
    let totalMaterialCost = 0;
    const materialTotalInputs = document.querySelectorAll('#bomTable .bom-total-cost');
            
    materialTotalInputs.forEach(input => {
        if (input.closest('tr').style.display !== 'none') { // 숨겨진 템플릿 행은 제외
            totalMaterialCost += parseFloat(input.value) || 0;
        }
    });

    const laborCost = parseFloat(document.getElementById('labor_cost').value) || 0;
    const indirectCost = parseFloat(document.getElementById('indirect_cost').value) || 0;
            
    const totalOtherCost = laborCost + indirectCost;
    const finalCost = totalMaterialCost + totalOtherCost;

    // 결과 표시
    document.getElementById('total_material_cost').value = totalMaterialCost.toFixed(2);
    document.getElementById('total_other_cost').value = totalOtherCost.toFixed(2);
    document.getElementById('final_cost').value = finalCost.toFixed(2);
}

const register = () => {    
    const formData = new FormData(frm);

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
        if(data != null || data != '') {
            if(data.result == 'success') {
                alert(data.message);
                location.href = '?controller=basic&action=listItem';
            }
        }
    })
    .catch(error => console.log(error));
}
</script>