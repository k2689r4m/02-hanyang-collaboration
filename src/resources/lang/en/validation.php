<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => ':attribute확인이 일치하지 않습니다.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => '정확한 :attribute을 입력해주세요.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => ':attribute은(는) 필수항목입니다.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => ':attribute는 :max자 이하여야 합니다.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => ':attribute은(는) 최소 :min 글자 여야합니다.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => ':attribute형식이 올바르지 않습니다.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => ':attribute형식이 올바르지 않습니다.',
    'required' => ':attribute을(를) 입력해주세요.',
    'required_if' => ':attribute을(를) 확인해주세요.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => '이미 사용중인 :attribute 입니다.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => '이메일',
        'contact' => '휴대폰번호',
        'password' => '비밀번호',
        'name' => '이름',
        'content' => '입력 값',
        'check1' => '이용약관 동의',
        'check2' => '정보수집 동의',
        'code' => '코드',
        'agency' => '소속기관',
        'agree1' => '개인정보수집활용 동의',
        'agree2' => 'IC-PBL 교과목 운영 지원 서약',
        'agree3' => '대학혁신지원사업 교과목 개발',
        'aplEmail' => 'e-mail',
        'aplName' => '성명',
        'aplOrg' => '소속',
        'aplPhone' => '핸드폰번호',
        'aplTel' => '연구실번호',
        'basic1' => '적정 수업 크기',
        'basic2' => '튜터 활용 여부와 활용 계획',
        'basic3' => '학습자 특성',
        'basic4' => '교실환경(H/W, S/W)',
        'basic5' => '교재 및 수업자료 활용 계획',
        'basic6' => '현장 연계 계획',
        'basicPlan' => '평가 계획',
        'conDescription' => '교육과정 개발 기여도 평가 내용',
        'conName' => '기여구분 성명',
        'conPer' => '기여구분 기여도',
        'department' => '개설학과',
        'description' => '교과목 개요',
        'duration' => '개발기간',
        'engName' => '영문 교과목명',
        'exercisePoint' => '실습',
        'expert' => '현장전문가 직급 및 업무분야',
        'grade' => '수강학년',
        'gradesPoint' => '학점',
        'korName' => '국문 교과목명',
        'lecturePoint' => '강의',
        'meca' => 'MECA 유형',
        'proSize1' => '교강사 수',
        'proSize2' => '교강사 수',
        'proSize3' => '교강사 수',
        'role1' => '참여 역할',
        'role2' => '참여 역할',
        'sceContent' => '학습 내용',
        'sceDetail' => '문제 상황 시나리오',
        'sceGoal' => '핵심 학습 목표',
        'sceRole' => '문제 상황 속 학습자(주인공) 역할',
        'sceTitle' => '시나리오 제목',
        'size1' => '수강규모',
        'size2' => '수강규모',
        'special' => '특수수업',
        'type' => '교과유형',
        'semester' => '개설학기',
        'college' => '개설단과대학',
        'lectureName' => '교과목명',
        'division' => '교과구분',
        'grades' => '학과(전공)/학년',
        'professor' => '교강사명',
        'size' => '수강인원',
        'icpblType' => 'IC-PBL 유형',
        'summary' => '교과목 개요',
        'classGoal' => '수업목표',
        'method' => '운영방식',
        'title' => '제목',
        'role' => '문제 상황 속 학습자(주인공) 역할',
        'scenario' => '문제 시나리오',
        'finalOutput' => '최종 수업 결과물',
        'mainStudent' => '주요 학생성찰',
        'sTitle' => '현장명',
        'sName' => '현장전문가 성명, 직급 및 업무분야',
        'sRole1' => '참여역할',
        'sRole2' => '참여역할',
        'sLink' => '현장연계사항',
        'sFeedback' => '환류/성과',
        'sOpinion' => '현장의견',
        'pr1' => '교과 선택',
        'pr2' => '문제 개발',
        'pr3' => '팀 구성',
        'pr4' => '퍼실리테이팅',
        'pr5' => '평가 방식',
        'pr6' => '기타 총평',
        'dateTime' => '미팅 일시',
        'problemSolvingProcess' => '문제해결과정',
        'attendees' => '참석자',
        'mainActivities' => '본 미팅의 주요활동',
        'task1' => '이번 미팅의 활동 내용',
        'task2' => '이번 미팅의 조치 사항',
        'discuss1' => '논의 사항',
        'schedule1' => '다음 미팅의 활동내용',
        'schedule2' => '역할 분담 ',
        'schedule3' => '다음 미팅의 조치 사항',
        'schedule4' => '기타',
        'schedule4' => '기타',
        'studentId' => '학번',
        'content1' => '1번 내용',
        'content2' => '2번 내용',
        'content3' => '3번 내용',
        'content4' => '4번 내용',
        'content5' => '5번 내용',
        'content6' => '6번 내용',
        'content7' => '7번 내용',
        'new_password' => '비밀번호',
        'expected8' => '기타 상세내용',
    ],

];
