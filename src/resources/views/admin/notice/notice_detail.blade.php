@extends('admin.layouts.admin')

@section('script')
    <script>
        window.onload = () => {
            document.getElementById('file').addEventListener('change', (e) => {
                if(e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const previewImage = document.getElementById("image");
                        previewImage.src = e.target.result;
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="card-body">
        <div class="card-body col-6">
        <form method="POST" action="{{ route('admin.noticeDetail', ['noticeId' => $notice->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="col-form-label">* 제목</label>
                <input class="form-control" type="text" name="title" value="{{ old('title') ?? $notice->title }}" />
                @error('title')
                <p class="text-danger">제목을 입력해 주세요</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="col-form-label">* 내용</label>
                <textarea class="form-control" name="content">{{ old('content') ?? $notice->content }}</textarea>
                @error('content')
                <p class="text-danger">내용을 입력해 주세요</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="col-form-label">* 이미지</label>
                <div class="form-control img"
                     onclick="document.getElementById('file').click()">
                <img id="image"
                     @if($notice->imagePathName)
                     src="{{ route('notice.image', ['imagePathName' => $notice->imagePathName]) }}"
                     @endif
                >
                <input id="file" type="file" style="display: none" name="image" />
                </div>
                @error('file')
                <p class="text-danger">이미지 형식이 맞지 않습니다.</p>
                @enderror
            </div>
            <br>
            <div class="text-center">
                <button class="btn btn-primary">저장</button>
            </div>
        </form>
        </div>
    </div>
@endsection