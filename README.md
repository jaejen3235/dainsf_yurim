# 유림농산 (MES)

PHP·MySQL 기반 제조 실행 시스템(MES) 웹 애플리케이션입니다. 세션 로그인 후 `controller`·`action` 파라미터로 화면을 구성하며, 모바일 User-Agent는 `mobile/` 경로로 분기합니다.

## 구성 요약

| 경로 | 설명 |
|------|------|
| `controllers/` | 비즈니스 로직·MES 등 컨트롤러 |
| `views/` | 화면·모달·레이아웃 |
| `apis/` | API 엔드포인트 |
| `include/` | DB 연결 등 공통 (`db_define.php`에서 DB 설정) |
| `mobile/` | 모바일 전용 진입 |
| `library/` | PHPExcel 등 서드파티 라이브러리 |
| `logapi/` | MES 사용 로그 전송·시뮬레이터(정부 연계용). 상세는 [logapi/README.md](logapi/README.md) 참고 |

## 환경

- PHP, MySQL(mysqli), UTF-8
- DB 접속 정보는 저장소에 커밋하지 말고 `include/db_define.php` 등 로컬 설정으로 관리하는 것을 권장합니다.

## 작업 기록

### 2026-04-02 (목)

- 프로젝트 루트에 본 `README.md`를 추가하고, 저장소 구조 요약과 아래 하위 프로젝트 작업 이력을 날짜별로 정리함.

### 2026-03-31 (화) — logapi

- `etc/logrotate.d/logapi` 로그 경로 조정 후 `logrotate -d`로 파싱·적용 대상(`logapi.log`, `simulator.log`) 검증.
- 시스템 `logrotate.conf`에 프로젝트 내 logrotate 설정 디렉터리 `include` 반영(다중 include 시 동작·중복 로테이션 주의사항 점검).

### 2026-03-30 (월) — logapi

- 시뮬레이터: 공휴일 파일 갱신·로드 캐시, 휴일 시 모든 `useSe` API 호출 스킵.
- crontab(월~금 09:00)에 `python3 -m logapi_py` 실행, PID 락으로 중복 실행 방지, 18:00 세션 로그아웃 후 종료.
- `LogApiCaller.is_success()`가 응답 `result.recptnRsltCd` 기준으로 `AP1002` 성공 판별하도록 수정.

---

이후 작업은 이 섹션에 날짜를 붙여 이어 적으면 됩니다.
