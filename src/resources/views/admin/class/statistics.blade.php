@extends('admin.layouts.classManage')

@section('_script')
    <script>
        function formChange(optionValue)
        {
            console.log(optionValue);
        }
    </script>
@endsection

@section('_content')
    <div class="card-body">
        <form class="text-right" method="get" action="{{ route('admin.adminExport') }}">
            <input type="hidden" name="gwamokNm" value="{{ request()->query('gwamokNm') ?? '' }}" />
            <input type="hidden" name="gnjSosokNm" value="{{ request()->query('gnjSosokNm') ?? '' }}" />
            <input type="hidden" name="gnjHakgwaNm" value="{{ request()->query('gnjHakgwaNm') ?? '' }}" />
            <input type="hidden" name="suupNo" value="{{ request()->query('suupNo') ?? '' }}" />
            <input type="hidden" name="daepyoGangsaNm" value="{{ request()->query('daepyoGangsaNm') ?? '' }}" />
            <input type="hidden" name="meca" value="{{ request()->query('meca') ?? '' }}" />
            <input type="hidden" name="state" value="statistics" />
            <input type="hidden" name="year" value="{{ $year }}" />
            <input type="hidden" name="term" value="{{ $term }}" />
            <button class="btn btn-md btn-primary">엑셀 다운</button>
        </form>
        <form method="GET" action="{{ route('admin.classStatisticsView') }}">
            <div class="card-body">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="8%" />
                        <col width="12%" />
                        <col width="8%" />
                        <col width="12%" />
                        <col width="8%" />
                        <col width="22%" />
                        <col width="8%" />
                        <col width="22%" />
                    </colgroup>
                    <tr>
                        <th>수업명</th>
                        <td colspan="3"><input class="form-control" type="text" name="gwamokNm" value="{{ request()->query('gwamokNm') }}" /></td>
                        <th>단대</th>
                        <td><input class="form-control" type="text" name="gnjSosokNm" value="{{ request()->query('gnjSosokNm') }}" /></td>
                        <th>학과</th>
                        <td><input class="form-control" type="text" name="gnjHakgwaNm" value="{{ request()->query('gnjHakgwaNm') }}" /></td>
                    </tr>
                    <tr>
                        <th>학기</th>
                        <td>
                            <select class="form-control w-sm" name="year">
                                <option value="" >선택</option>
                                @foreach(range(2010, 2030) as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}년</option>
                                @endforeach
                            </select>
                            <select class="form-control w-sm" name="term">
                                <option value="">
                                    선택
                                </option>
                                <option value="10" {{ $term == '10' ? 'selected' : '' }}>
                                    1학기
                                </option>
                                <option value="15" {{ $term == '15' ? 'selected' : '' }}>
                                    여름학기
                                </option>
                                <option value="20" {{ $term == '20' ? 'selected' : '' }}>
                                    2학기
                                </option>
                                <option value="25" {{ $term == '25' ? 'selected' : '' }}>
                                    겨울학기
                                </option>
                            </select>
                        </td>
                        <th>유형</th>
                        <td>
                            <select class="form-control w-100" name="meca">
                                <option @if(request()->query('meca') == '') selected @endif>선택</option>
                                <option @if(request()->query('meca') == 'M') selected @endif>M</option>
                                <option @if(request()->query('meca') == 'E') selected @endif>E</option>
                                <option @if(request()->query('meca') == 'C') selected @endif>C</option>
                                <option @if(request()->query('meca') == 'A') selected @endif>A</option>
                            </select>
                        </td>
                        <th>수업번호</th>
                        <td><input class="form-control" type="text" name="suupNo" value="{{ request()->query('suupNo') }}" /></td>
                        <th>교수명</th>
                        <td><input class="form-control" type="text" name="daepyoGangsaNm" value="{{ request()->query('daepyoGangsaNm') }}" /></td>
                    </tr>
                </table>
            </div>
            <div class="text-right card-body">
                <div class="float-left col-1">
                    <form method="get" action="{{ route('admin.classStatisticsView')}} ">
                        <select class="form-control" name="choice" onchange="submit()" >
                            <option type="submit" value="" @if(request()->query('choice') == '') selected @endif>선택</option>
                            <option type="submit" value="all" @if(request()->query('choice') == 'all') selected @endif>전체</option>
                            <option type="submit" value="gnjSosokNm" @if(request()->query('choice') == 'gnjSosokNm') selected @endif>단대</option>
                            <option type="submit" value="class" @if(request()->query('choice') == 'class') selected @endif>수업</option>
                        </select>
                    </form>
                </div>
                <button class="btn btn-primary" >검색</button>
                <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('admin.classStatisticsView') }}'">초기화</button>
            </div>
        </form>
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="5%" /> {{--번호--}}
                    <col width="10%" /> {{--코드--}}
                    <col width="10%" /> {{--수업명--}}
                    <col width="10%" /> {{--단대--}}
                    <col width="10%" /> {{--학과--}}
                    <col width="10%" /> {{--교수명--}}
                    <col width="10%" /> {{--학기--}}
                    <col width="5%" /> {{--유형--}}
                    <col width="5%" /> {{--학생수--}}
                    <col width="5%" /> {{--팀수--}}
                    <col width="5%" /> {{--활동점수--}}
                    <col width="5%" /> {{--활동점수--}}
                    <col width="5%" /> {{--활동점수--}}
                    <col width="5%" /> {{--총점--}}
                </colgroup>
                <thead>
                    <th>번호</th>
                    <th>수업번호</th>
                    <th>수업명</th>
                    <th>단대</th>
                    <th>학과</th>
                    <th>교수명</th>
                    <th>학기</th>
                    <th>유형<br>
                        Before
                        After
                    </th>
                    <th>학생 수</th>
                    <th>팀수</th>
                    <th colspan="3">활동점수<br>
                        협력 성취 피드백
                    </th>
                    <th>총점</th>
                </thead>
                <tbody>
                    @foreach($classApplies as $classApply)
                        <tr>
                            <td>{{ $classApply->id }}</td>
                            <td>{{ $classApply->suupNo }}</td>
                            <td><a href="{{ route('admin.classDetail1View', ['classApplyId' => $classApply->classApplyId]) }}">{{ $classApply->korName }}</a></td>
                            <td>{{ $classApply->gnjDaehakNm }}</td>
                            <td>{{ $classApply->gnjHakgwaNm }}</td>
                            <td>{{ $classApply->itemUser->user->name }}</td>
                            <td>@if($classApply->suupYear && $classApply->suupTerm)
                                    {{ $classApply->suupYear.'년도' }}
                                    {{ $classApply->suupTerm == '10' ? '1학기' :
                                    ($classApply->suupTerm == '15' ? '여르학기' :
                                    ( $classApply->suupTerm == '20' ? '2학기' :
                                    ( $classApply->suupTerm == '25' ? '겨울학기' : ''))) }}
                                @endif
                            </td>
                            <td>@if($classApply->meca == '1')
                                    M
                                @elseif($classApply->meca == '2')
                                    E
                                @elseif($classApply->meca == '3')
                                    C
                                @elseif($classApply->meca == '4')
                                    C
                                @else
                                    none
                                @endif</td>{{--유형--}}
                            <td>{{ $classApply->classListCount2() }}</td>{{--학생수--}}
                            <td>{{ $classApply->teamCount }}</td>{{--팀수--}}
                            <td>{{ $classApply->scores['co'] }}</td>{{--협력--}}
                            <td>{{ $classApply->scores['ac'] }}</td>{{--성취--}}
                            <td>{{ $classApply->scores['fe'] }}</td>{{--피드--}}
{{--                            <td>{{ $classObject->teamCount() }}</td>--}}
                            <td>{{ $classApply->scores['co'] + $classApply->scores['ac'] + $classApply->scores['fe'] }}</td>{{--총점--}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
                {{ $classApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection