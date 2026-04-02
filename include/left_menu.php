<?php
// 메뉴 배열 정의
$left_menu = [
    '기준정보 관리' => [
        'icon' => "<i class='bx bx-food-menu'></i>",
        'submenus' => [
            [
                'controller' => 'basic',
                'action' => 'settingBasic',
                'title' => '기본 단위 설정'
            ],
            [
                'controller' => 'basic',
                'action' => 'listItem',
                'title' => '품목 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listAccount',
                'title' => '거래처 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listEmployee',
                'title' => '사원 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listBom',
                'title' => 'BOM 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listDefect',
                'title' => '불량유형 관리'
            ]
        ]
    ],
    '영업 관리' => [
        'icon' => "<i class='bx bx-folder-plus'></i>",
        'submenus' => [
            [
                'controller' => 'sales',
                'action' => 'listOrder',
                'title' => '수주정보 관리'
            ],
            [
                'controller' => 'sales',
                'action' => 'listShipment',
                'title' => '출하지시 관리'
            ],
            [
                'controller' => 'sales',
                'action' => 'searchShipment',
                'title' => '출하현황 조회'
            ]
        ]
    ],
    '자재 관리' => [
        'icon' => "<i class='bx bx-cube'></i>",
        'submenus' => [
            [
                'controller' => 'items',
                'action' => 'listInItem',
                'title' => '입고 관리'
            ],
            [
                'controller' => 'items',
                'action' => 'stockManagement',
                'title' => '재고 관리'
            ],
            [
                'controller' => 'items',
                'action' => 'reportInOutItem',
                'title' => '자재 수불부'
            ],
            [
                'controller' => 'items',
                'action' => 'listPurchase',
                'title' => '자재요청/발주관리'
            ],
        ]
    ],
    '생산 관리' => [
        'icon' => "<i class='bx bx-barcode-reader'></i>",
        'submenus' => [
            [
                'controller' => 'product',
                'action' => 'workOrderManagement',
                'title' => '생산지시서 관리'
            ],
            [
                'controller' => 'product',
                'action' => 'workOrderCalendar',
                'title' => '작업일정 계획'
            ],
            [
                'controller' => 'product',
                'action' => 'reportDayWork',
                'title' => '작업일보 관리'
            ],
            [
                'controller' => 'product',
                'action' => 'reportProduct',
                'title' => '생산실적 관리'
            ],
        ]
    ],    
    '품질 관리' => [
        'icon' => "<i class='bx bxs-flask'></i>",
        'submenus' => [
            [
                'controller' => 'quality',
                'action' => 'importInspection',
                'title' => '수입검사 관리'
            ],
            [
                'controller' => 'quality',
                'action' => 'qualityInspection',
                'title' => '품질검사'
            ],
            [
                'controller' => 'quality',
                'action' => 'metalInspection',
                'title' => '금속검출 관리'
            ],
            [
                'controller' => 'quality',
                'action' => 'reworkList',
                'title' => '리워크 관리'
            ],
            [
                'controller' => 'quality',
                'action' => 'inspectionList',
                'title' => '검사이력조회'
            ]
        ]
    ],   
    '출하 관리' => [
        'icon' => "<i class='bx bx-bus-school'></i>",
        'submenus' => [
            [
                'controller' => 'shipment',
                'action' => 'shipmentManagement',
                'title' => '출하 지시서'
            ],
            [
                'controller' => 'shipment',
                'action' => 'deliveryManagement',
                'title' => '출하 관리'
            ],
        ]
    ],
    '전기에너지 관리' => [
        'icon' => "<i class='bx bx-selection'></i>",
        'submenus' => [
            [
                'controller' => 'electricity',
                'action' => 'totalPower',
                'title' => '종합 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'timePower',
                'title' => '시간대별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'dayPower',
                'title' => '일별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'monthPower',
                'title' => '월별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'yearPower',
                'title' => '연도별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'peakPower',
                'title' => '설비별 피크 전력 관리'
            ]
        ]
    ],
    'KPI지표 관리' => [
        'icon' => "<i class='bx bx-camera-home'></i>",
        'submenus' => [
            [
                'controller' => 'kpi',
                'action' => 'defectRate',
                'title' => '완제품불량률'
            ],
            [
                'controller' => 'kpi',
                'action' => 'inventoryCost',
                'title' => '제품원가    '
            ],
        ]
    ],
    '모니터링' => [
        'icon' => "<i class='bx bx-camera-home'></i>",
        'submenus' => [
            [
                'controller' => 'monitoring',
                'action' => 'workOrder',
                'title' => '생산 현황'
            ],
            [
                'controller' => 'monitoring',
                'action' => 'leakageInspection',
                'title' => '금속검출현황'
            ],
            [
                'controller' => 'monitoring',
                'action' => 'ems',
                'title' => '전력사용현황'
            ],
        ]
    ],
    '시스템 관리' => [
        'icon' => "<i class='bx bx-cog'></i>",
        'submenus' => [
            [
                'controller' => 'system',
                'action' => 'configSystem',
                'title' => '사용자 설정'
            ],
            [
                'controller' => 'system',
                'action' => 'loginReport',
                'title' => '로그인 이력'
            ],
            [
                'controller' => 'system',
                'action' => 'setting',
                'title' => '환경설정'
            ]
        ]
    ],
];

// 로그인 레벨이 1000일 경우 새로운 메뉴 추가
if ($_SESSION['loginLevel'] == 1000) {
    $left_menu['시스템 관리']['submenus'][] = [
        'controller' => 'system',
        'action' => 'dbManagement',
        'title' => 'DB 설정'
    ];
    $left_menu['시스템 관리']['submenus'][] = [
        'controller' => 'system',
        'action' => 'addAdmin',
        'title' => '관리자 계정 설정'
    ];
    $left_menu['시스템 관리']['submenus'][] = [        
        'controller' => 'system',
        'action' => 'createLoginReport',
        'title' => '로그인 이력 생성'
    ];
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createPowerData',
        'title' => '전력데이터 생성'
    ];
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createMonthPowerData',
        'title' => '월별 전력데이터 생성'
    ];
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createDayPowerData',
        'title' => '일별 전력데이터 생성'
    ];
}


// 메뉴 출력
// URL 파라미터 가져오기
$currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';

$tag = '<div class="menu">';
foreach ($left_menu as $menu_title => $menu_data) {
    $tag .= "<div class='menu-item'>";
    $tag .= "<div class='menu-section'>";
    $tag .= "<div class='menu-title-box'>";
    $tag .= $menu_data['icon'];

    // 상위 메뉴의 활성화 클래스 추가
    $isActiveMenu = false; // 상위 메뉴 활성화 여부
    foreach ($menu_data['submenus'] as $submenu) {
        if ($currentController === $submenu['controller'] && $currentAction === $submenu['action']) {
            $isActiveMenu = true; // 서브메뉴가 활성화된 경우
            break;
        }
    }
    $menuActive = $isActiveMenu ? 'active' : '';

    $tag .= "<a href='#' class='menu-title {$menuActive}'>{$menu_title}</a>";
    $tag .= "</div>";
    $tag .= "<div><i class='bx bx-chevron-down'></i></div>";
    $tag .= "</div>";

    $submenuActive = ($currentController === $submenu['controller']) ? 'active' : '';

    $tag .= "<ul class='submenu {$submenuActive}'>";
    foreach ($menu_data['submenus'] as $submenu) {
        // 현재 서브메뉴에 active 클래스를 추가
        $itemActive = ($currentAction === $submenu['action']) ? 'active' : '';

        $tag .= "<li class='{$itemActive}'><i class='bx bx-chevron-right'></i> <a href='#'  onclick=\"movePage('{$submenu['controller']}', '{$submenu['action']}')\">{$submenu['title']}</a></li>";
    }
    $tag .= "</ul>";
    $tag .= "</div>";
}
$tag .= '</div>';

echo $tag;
?>
