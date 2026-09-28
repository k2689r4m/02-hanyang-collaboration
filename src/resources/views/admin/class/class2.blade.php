
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
            <input type="hidden" name="state" value="complete" />
            <button class="btn btn-md btn-primary">엑셀 다운</button>
        </form>
    <form method="GET" action="{{ route('admin.class2View') }}">
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
            <button class="btn btn-primary">검색</button>
            <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('admin.class2View') }}'">초기화</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.class2') }}">
        @csrf
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="5%" />{{--번호--}}
                    <col width="10%" />{{--수업번호--}}
                    <col width="30%" />{{--수업명--}}
                    <col width="10%" />{{--유형--}}
                    <col width="15%" />{{--단대--}}
                    <col width="10%" />{{--학과--}}
                    <col width="10%" />{{--학기--}}
                    <col width="10%" />{{--교수명--}}
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
                </thead>
                <tbody>
                @foreach($classApplies as $key=>$classApply)
                    <tr>
{{--                        <td>{{ ($classApplies->currentPage() - 1) * $classApplies->perPage() + ($key + 1) }}</td>--}}
                        <td>{{ $classApply->id }}</td>
                        <td>{{ $classApply->code }}</td>
                        <td><a href="{{ route('admin.classDetail1View', ['classApplyId' => $classApply->id]) }}">{{ $classApply->korName }}</a></td>
{{--                        <td>{{ $classApply->gnjDaehakNm }}</td>--}}
                        <td>{{ $classApply->itemUser->user->sosokNm }}</td>
                        <td>{{ $classApply->department }}</td>
                        <td>
{{--                            @if($classApply->classObject){{ $classApply->classObject->suupYear }} 년@else @endif--}}
                            {{ $classApply->classObject ? $classApply->classObject->suupYear.'년' : '' }}
                            @if($classApply->suupTerm == '10')
                                1학기
                            @elseif($classApply->suupTerm == '15')
                                여름학기
                            @elseif($classApply->suupTerm == '20')
                                2학기
                            @elseif($classApply->suupTerm == '25')
                                겨울학기
                            @endif</td>
                        <td>{{ $classApply->meca == '1' ? 'M' :
                            ( $classApply->meca == '2' ? 'E' :
                            ( $classApply->meca == '3'? 'C' :
                            ( $classApply->meca == '4' ? 'A' : ''))) }}</td>
                        <td>{{ $classApply->itemUser->user->name }}</td>
                    </tr>
                @endforeach
{{--                @foreach($classApplies as $key=>$classApply)--}}
{{--                    <tr>--}}
{{--                        <td>{{ ($classApplies->currentPage() - 1) * $classApplies->perPage() + ($key + 1) }}</td>--}}
{{--                        <td>{{ $classApply->classApplyId }}</td>--}}
{{--                        <td>{{ $classApply->suupNo }}</td>--}}
{{--                        <td><a href="{{ route('admin.classDetail1View', ['classApplyId' => $classApply->classApplyId]) }}">{{ $classApply->korName }}</a></td>--}}
{{--                        <td>{{ $classApply->gnjDaehakNm }}</td>--}}
{{--                        <td>{{ $classApply->gnjHakgwaNm }}</td>--}}
{{--                        <td>{{ $classApply->suupYear }}년--}}
{{--                            @if($classApply->suupTerm == '10')--}}
{{--                                1--}}
{{--                            @elseif($classApply->suupTerm == '15')--}}
{{--                                여름--}}
{{--                            @elseif($classApply->suupTerm == '20')--}}
{{--                                2--}}
{{--                            @elseif($classApply->suupTerm == '25')--}}
{{--                                겨울--}}
{{--                            @endif학기</td>--}}
{{--                        <td>{{ $classApply->itemUser->user->name }}</td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                </tbody>
            </table>
            {{ $classApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
    </form>
    </div>
@endsection