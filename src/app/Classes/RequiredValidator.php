<?php

namespace App\Classes;

use Illuminate\Support\Facades\Validator;

class RequiredValidator
{
    private $attributes = [
        //classApply
        'classApply' => [
            'type' => ['교과유형', 'boolean'],
            'grade' => ['수강학년', 'boolean'],
            'size1' => ['수강규모', 'boolean'],
            'proSize1' => ['교강사 수', 'boolean'],
            'department' => ['개설학과', 'string'],
            'major' => ['전공', 'string'],
            'special' => ['특수수업', 'boolean'],
            'special2' => ['특수수업', 'boolean'],
            'special3' => ['특수수업', 'boolean'],
            'special4' => ['특수수업', 'boolean'],
            'korName' => ['국문 교과목명', 'string'],
            'engName' => ['영문 교과목명', 'string'],
            'gradesPoint' => ['학점', 'string'],
            'lecturePoint' => ['강의', 'string'],
            'exercisePoint' => ['실습', 'string'],
            'description' => ['교과목 개요', 'string'],
            'meca' => ['MECA 유형', 'boolean'],
            'role1' => ['참여 역할', 'boolean'],
            'role3' => ['참여 역할', 'boolean'],
            'role4' => ['참여 역할', 'boolean'],
            'role5' => ['참여 역할', 'boolean'],
            'expected1' => ['예상수업결과물', 'boolean'],
            'expected2' => ['예상수업결과물', 'boolean'],
            'expected3' => ['예상수업결과물', 'boolean'],
            'expected4' => ['예상수업결과물', 'boolean'],
            'expected5' => ['예상수업결과물', 'boolean'],
            'expected6' => ['예상수업결과물', 'boolean'],
            'expected7' => ['예상수업결과물', 'boolean'],
            'aplName' => ['성명', 'string'],
            'aplOrg' => ['소속', 'string'],
            'aplTel' => ['연구실번호', 'string'],
            'aplPhone' => ['핸드폰번호', 'string'],
            'aplEmail' => ['e-mail', 'string'],
            'agree1' => ['개인정보수집활용 동의', 'boolean'],
            'basic1' => ['적정 수업 크기', 'string'],
            'basic2' => ['튜터 활용 여부와 활용 계획', 'string'],
            'basic3' => ['학습자 특성', 'string'],
            'basic4' => ['교실환경(H/W, S/W)', 'string'],
            'basic5' => ['교재 및 수업자료 활용 계획', 'string'],
            'basic6' => ['현장 연계 계획', 'string'],
            'basicPlan' => ['평가 계획', 'string'],
            'sceContent' => ['학습 내용', 'string'],
            'sceGoal' => ['핵심 학습 목표', 'string'],
            'sceTitle' => ['시나리오 제목', 'string'],
            'sceRole' => ['문제 상황 속 학습자(주인공) 역할', 'string'],
            'sceDetail' => ['문제 상황 시나리오', 'string'],
            'planDetail' => ['세부 수업 진행 계획', 'string'],
        ],

        //operationResult
        'operationResult' => [
            'semester' => ['개설학기', 'string'],
            'college' => ['개설단과대학', 'string'],
            'lectureName' => ['교과목명', 'string'],
            'grade' => ['학점-강의-실습', 'string'],
            'division' => ['교과구분', 'boolean'],
            'grades' => ['학과(전공)/학년', 'string'],
            'professor' => ['교강사명', 'string'],
            'size' => ['수강인원', 'string'],
            'icpblType' => ['IC-PBL 유형', 'boolean'],
            'summary' => ['교과목 개요', 'string'],
            'classGoal' => ['수업목표', 'string'],
            'method' => ['운영방식', 'string'],
            'basicPlan' => ['평가 계획', 'string'],
            'title' => ['문제 시나리오 제목', 'string'],
            'role' => ['문제 상황 속 학습자(주인공) 역할', 'string'],
            'scenario' => ['문제 시나리오', 'string'],
            'outputType1' => ['결과물 유형', 'boolean'],
            'outputType2' => ['결과물 유형', 'boolean'],
            'outputType3' => ['결과물 유형', 'boolean'],
            'outputType4' => ['결과물 유형', 'boolean'],
            'outputType5' => ['결과물 유형', 'boolean'],
            'outputType6' => ['결과물 유형', 'boolean'],
            'outputType7' => ['결과물 유형', 'boolean'],
            'finalOutput' => ['최종 수업 결과물', 'string'],
            'mainStudent' => ['주요 학생성찰', 'string'],
            'sTitle' => ['현장명', 'string'],
            'sName' => ['현장전문가 성명, 직급 및 업무분야', 'string'],
            'sRole1' => ['참여역할', 'boolean'],
            'sLink' => ['현장연계사항', 'string'],
            'sFeedback' => ['환류/성과', 'string'],
            'sOpinion' => ['현장의견', 'string'],
            'pr1' => ['교과 선택', 'string'],
            'pr2' => ['문제 개발', 'string'],
            'pr3' => ['팀 구성', 'string'],
            'pr4' => ['퍼실리테이팅', 'string'],
            'pr5' => ['평가 방식', 'string'],
            'pr6' => ['기타 총평', 'string'],
        ],
    ];

    public function __construct()
    {
        //
    }

    public function validate($data, $attr, $type)
    {
        $checkList = [];
        foreach ($attr as $attribute) {
            $checkList[$attribute] = ['required'];
        }

        $validator = Validator::make($data, $checkList);

        if ($validator->fails()) {
            $errors = $validator->errors();

            foreach ($errors->messages() as $key=>$value) {
                if ($this->attributes[$type][$key][1] == 'string') {
                    return [$key => [$this->attributes[$type][$key][0].'을(를) 입력해주세요.']];
                }
                else if ($this->attributes[$type][$key][1] == 'boolean') {
                    return [$key => [$this->attributes[$type][$key][0].'을(를) 선택해주세요.']];
                }
            }
        }

        return false;
    }
}
