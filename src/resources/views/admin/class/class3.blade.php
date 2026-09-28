@extends('admin.layouts.classManage')

@section('_script')
@endsection

@section('_content')
    <div class="card-body">
        <form class="text-right" method="get" action="{{ route('admin.adminExport') }}">
            <input type="hidden" name="gwamokNm" value="{{ request()->query('gwamokNm') ?? '' }}" />
            <input type="hidden" name="gnjSosokNm" value="{{ request()->query('gnjSosokNm') ?? '' }}" />
            <input type="hidden" name="gnjHakgwaNm" value="{{ request()->query('gnjHakgwaNm') ?? '' }}" />
            <input type="hidden" name="year" value="{{ request()->query('year') ?? '' }}" />
            <input type="hidden" name="term" value="{{ request()->query('term') ?? '' }}" />
            <input type="hidden" name="suupNo" value="{{ request()->query('suupNo') ?? '' }}" />
            <input type="hidden" name="daepyoGangsaNm" value="{{ request()->query('daepyoGangsaNm') ?? '' }}" />
            <input type="hidden" name="meca" value="{{ request()->query('meca') ?? '' }}" />
            <input type="hidden" name="state" value="ing" />
            <button class="btn btn-md btn-primary">엑셀 다운</button>
        </form>
        <form method="GET" action="{{ route('admin.class3View') }}">
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
                    <form method="get" action="{{ route('admin.class3View')}} ">
                        <select class="form-control" name="choice" onchange="submit()" >
                            <option type="submit" value="" @if(request()->query('choice') == '') selected @endif>선택</option>
                            <option type="submit" value="all" @if(request()->query('choice') == 'all') selected @endif>전체</option>
                            <option type="submit" value="gnjSosokNm" @if(request()->query('choice') == 'gnjSosokNm') selected @endif>단대</option>
                            <option type="submit" value="class" @if(request()->query('choice') == 'class') selected @endif>수업</option>
                        </select>
                    </form>
                </div>
                <button class="btn btn-primary">검색</button>
                <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('admin.class3View') }}'">초기화</button>
            </div>
        </form>
        <div class="card-body">
            <form method="POST" action="">
                @csrf
                <ul class="nav nav-tabs table-tab">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.class3ProfessorView') }}">교수학습 현황</a></li>
                    <li class="nav-item"><a class="nav-link active" href="">수업현황</a></li>
                </ul>
                <div class="card-body">
                    <table class="table table-hover text-center">
                        <colgroup>
                            <col width="4%" />{{--번호--}}
                            <col width="6%" />{{--수업번호--}}
                            <col width="15%" />{{--수업명--}}
                            <col width="10%" />{{--단대--}}
                            <col width="12%" />{{--학과--}}
                            <col width="8%" />{{--학기--}}
                            <col width="4%" />{{--유형--}}
                            <col width="10%" />{{--교수명--}}
                            <col width="7%" />{{--문제분석--}}
                            <col width="4%" />{{--팀활동--}}
                            <col width="4%" />{{--평가--}}
                            <col width="4%" />{{--성찰--}}
                            <col width="4%" />{{--협력--}}
                            <col width="4%" />{{--성취--}}
                            <col width="4%" />{{--피드백--}}
                        </colgroup>
                        <thead>
                        <th>번호</th>
                        <th>수업번호</th>
                        <th>수업명</th>
                        <th>단대</th>
                        <th>학과</th>
                        <th>학기</th>
                        <th>유형</th>
                        <th>교수명</th>
                        <th>문제분석</th>
                        <th>팀활동</th>
                        <th>평가</th>
                        <th>성찰</th>
                        <th>협력</th>
                        <th>성취</th>
                        <th>피드백</th>
                        </thead>
                        <tbody>

                        @foreach($classApplies as $classApply)
                            <?php
                            $problems = 0;
                            $reflections = 0;
                            $teams = 0;
                            ?>
                            @foreach($classApply->prt as $prt)
                                @foreach($prt->items2 as $item)
                                    @switch($item->type)
                                        @case(1)
                                        <?php
                                        $problems++;
                                        ?>
                                        @break
                                        @case(2)
                                        <?php
                                        $reflections++;
                                        ?>
                                        @break
                                        @case(5)
                                        <?php
                                        $teams++;
                                        ?>
                                        @break
                                    @endswitch
                                @endforeach
                            @endforeach
                            <tr>
                                <td>{{ $classApply->id }}</td>
                                <td>{{ $classApply->suupNo }}</td>
                                <td><a href="{{ route('admin.classDetail1View', ['classApplyId' => $classApply->classApplyId]) }}">{{ $classApply->korName }}</a></td>
                                <td>{{ $classApply->gnjDaehakNm }}</td>
                                <td>{{ $classApply->gnjHakgwaNm }}</td>
                                <td>{{ $classApply->suupYear ? $classApply->suupYear.'년' : '' }}
                                    {{ $classApply->suupTermNm }}</td>
                                <td>{{ $classApply->meca }}</td>
                                <td>{{ $classApply->daepyoGangsaNm }}</td>
                                <td>{{ $problems }}</td>
                                <td>{{ $teams }}</td>
                                <td>no</td>
                                <td>{{ $reflections }}</td>
                                <td>{{ $classApply->scores['co'] }}</td>
                                <td>{{ $classApply->scores['ac'] }}</td>
                                <td>{{ $classApply->scores['fe'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $classApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
                </div>
            </form>
        </div>
    </div>
@endsection