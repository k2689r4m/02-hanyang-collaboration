## 프로젝트명
한양대학교 IC-PBL 실시간 협업 플랫폼

## 수행 시기
2021년

## 프로젝트 소개
한양대학교 IC-PBL 수업 및 팀 프로젝트를 지원하기 위한 실시간 협업 플랫폼.

Trello와 유사한 카드 기반 협업 방식을 적용하여 수업 및 팀별로 업무와 활동을 관리할 수 있도록 개발하였다.

사용자는 협업 보드에서 Card와 Item(Task)을 생성하고 Drag & Drop으로 위치와 진행 상태를 변경할 수 있으며, 담당자, 마감일, 라벨, 체크리스트, 첨부파일, 댓글 등을 이용하여 팀 단위 업무를 관리할 수 있다.

WebSocket 기반 실시간 동기화를 적용하여 여러 사용자가 동시에 접속한 상태에서 카드 및 작업의 생성·수정·이동·삭제, 댓글, 채팅, 일정 등의 변경사항이 다른 참여자의 화면에도 즉시 반영되도록 구현하였다.

단순 협업 보드뿐 아니라 문제 분석, 팀 활동, 성찰일지, 평가, 컨설팅 등 IC-PBL 교육 과정에서 필요한 다양한 활동 및 관리 기능을 통합한 교육용 협업 시스템이다.

## 주요 기술
- PHP / Laravel 8
- Vue.js 2
- JavaScript
- Laravel WebSockets
- Laravel Echo
- Pusher Protocol
- Presence Channel
- MySQL 계열 관계형 DB
- Axios
- Vue Draggable
- Tiptap
- Laravel Mix
- HTML / SCSS / Bootstrap

## 주요 기능

### 실시간 협업 보드
- 수업 및 팀 단위 협업 공간 제공
- Card 기반 업무 분류 및 관리
- Item(Task) 생성·수정·삭제
- Drag & Drop 기반 Card 순서 변경
- Drag & Drop 기반 Task 순서 및 상태 변경
- 서로 다른 Card 간 Task 이동
- 여러 사용자의 작업 변경사항 실시간 동기화

### Task 상세 관리
- 업무 제목 및 상세 내용 작성
- 담당자 지정
- 마감일 설정
- 업무별 라벨 지정
- 체크리스트 관리
- 이미지 첨부
- 파일 첨부
- 댓글 작성 및 수정·삭제
- 업무별 세부 상태 관리

### WebSocket 실시간 동기화
- Laravel WebSockets 기반 실시간 통신 환경 구성
- Laravel Echo 및 Presence Channel을 이용한 사용자별 실시간 연결 관리
- 수업 및 팀별 WebSocket Channel 분리
- Card 생성·수정·이동·삭제 실시간 동기화
- Item 생성·수정·이동·삭제 실시간 동기화
- 댓글 생성·수정·삭제 실시간 반영
- 실시간 채팅
- 일정 변경 실시간 반영
- 사용자 활동 및 상태 관련 이벤트 처리

### 실시간 이벤트 구조
기능별 WebSocket Event를 분리하여 협업 기능의 변경사항을 관리.

- CardEvent
- ItemEvent
- CommentEvent
- ChattingEvent
- CalendarEvent
- BrainEvent
- FixedCardItemEvent
- 사용자 및 활동 Log 관련 Event

Card와 Item의 생성, 수정, 이동, 삭제 등의 이벤트를 구분하여 필요한 데이터만 각 참여자의 화면에 반영하도록 구현하였다.

### 팀 협업 및 커뮤니케이션
- 수업별 협업 공간
- 팀별 독립 협업 공간
- 팀원 관리
- 실시간 채팅
- 댓글 기반 업무 커뮤니케이션
- 일정 및 Calendar 관리
- 사용자 활동 기록

### IC-PBL 교육 기능
- 문제 분석
- 팀 활동 관리
- 성찰일지
- 평가
- 컨설팅
- 수업 관련 신청 및 활동 관리
- 교수/학생별 활동 관리
- 교육 과정에 필요한 다양한 양식 및 데이터 관리

## 담당 업무
**Frontend / DB 설계 / WebSocket 전체 담당**

일반적인 Laravel Backend API 및 서버 비즈니스 로직을 제외한 프론트엔드와 실시간 협업 시스템 영역을 담당하였다.

### Frontend
- Vue.js 기반 협업 플랫폼 프론트엔드 개발
- 수업/팀별 협업 보드 UI 구현
- Card 및 Item(Task) 관리 화면 구현
- Drag & Drop 기반 업무 이동 및 순서 변경 기능 구현
- Task 상세 관리 UI 구현
- 담당자, 마감일, 라벨, 체크리스트, 파일 및 이미지 관리 UI 구현
- 댓글 및 실시간 채팅 UI 구현
- 일정 및 Calendar UI 구현
- IC-PBL 교육 활동 관련 화면 구현
- 실시간 이벤트 수신에 따른 Vue 상태 및 화면 데이터 동기화

### DB 설계
- 사용자, 수업, 팀, Card, Item 등 협업 시스템의 주요 데이터 구조 설계
- Task 담당자, 마감일, 체크리스트, 첨부파일, 댓글 등 협업 기능을 위한 데이터 구조 설계
- IC-PBL 교육 활동 및 관련 데이터 구조 설계
- 실시간 협업 기능과 서버 데이터의 일관성을 고려한 관계형 DB 구조 설계

### WebSocket / 실시간 통신
- 프로젝트 WebSocket 기능 전체 설계 및 구현
- Laravel WebSockets 서버 구성
- Laravel Echo 기반 클라이언트 실시간 통신 구현
- Presence Channel 기반 실시간 사용자 연결 관리
- 수업/팀/사용자 목적에 따른 Channel 구조 설계
- Card / Item / Comment / Chat / Calendar 등 기능별 Event 구조 구현
- Card 및 Task의 생성·수정·이동·삭제 실시간 동기화
- Drag & Drop으로 변경된 작업 순서와 상태를 다른 사용자에게 실시간 반영
- 댓글 및 채팅 실시간 동기화
- 일정 및 사용자 활동 관련 이벤트 실시간 처리
- 실시간 이벤트 수신 후 Vue 데이터 구조를 갱신하여 전체 페이지를 다시 불러오지 않고 화면 상태 반영

## 프로젝트 내 역할
첫 번째 실무 프로젝트에서 경험한 Vue.js 및 WebSocket 기반 실시간 개발 경험을 확장하여, 두 번째 프로젝트에서는 보다 복잡한 다중 사용자 협업 환경의 프론트엔드와 실시간 통신 시스템을 담당하였다.

특히 Card, Item, Comment, Chat, Calendar 등 기능별 WebSocket 이벤트를 구성하고 수업 및 팀 단위의 독립적인 실시간 Channel을 설계하여 여러 사용자가 동시에 협업하는 환경에서 변경사항이 즉시 동기화되도록 구현하였다.

또한 서비스의 주요 DB 구조를 설계하고 Vue.js 기반 협업 보드 및 IC-PBL 관련 사용자 화면 전반을 개발하였다.

일반 Laravel Backend API 및 주요 서버 비즈니스 로직은 다른 개발자가 담당하였다.
