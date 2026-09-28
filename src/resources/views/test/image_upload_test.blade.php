<p>click image to download</p>

@foreach($files as $file)
    <img
            src="{{ asset('storage/uploads/'.$file) }}"
            style="height: 200px; cursor: pointer"
            onclick="location.href = '{{ route('home').'/download/'.$file }}';"
    />
    <img
            src="http:\/\/192.168.0.7:8000\/storage\/uploads\/1\/602f0a91db7a2"
            style="height: 200px; cursor: pointer"
            onclick="location.href = '{{ route('home').'/download/'.$file }}';"
    />
@endforeach


{{--<h1>{{ asset('storage/uploads/'.$file) }}</h1>--}}

{{--<form method="POST" action="{{ route('imageUpload') }}" enctype="multipart/form-data">--}}
{{--    @csrf--}}
{{--    myPageId: <input type="text" name="myPageId" value="{{ $myPage->id }}" readonly/>--}}
{{--    <div>--}}
{{--        image select: <input type="file" name="image" />--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <button>submit</button>--}}
{{--    </div>--}}
{{--</form>--}}
