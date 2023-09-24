@extends('app')
@section('content')
@include('partials.navbar')

<div class="mt-[120px] mx-[30px] md:mx-[78px]">
  {{-- img --}}
  <div class="img-project flex justify-center items-center mb-6">
    <img src={{$data->thumbnail_link}} class="rounded-xl min-w-[240px] max-w-[500px] shadow-sm">
  </div>
  {{-- info --}}
  <div class="info mt-[40px] mb-10">
    <p class="text-gray-400">2 min read | 30 Sept 2023</p>
  </div>
  {{-- paragraph --}}
  <div class="paragraph mb-[40px]">
    <h3 class="text-hitam font-semibold text-[24px] mb-3">{{$data["judul"]}}</h3>
    <p class="text-hitam-400 text-justify text-[14px] md:text-[16px] mb-8">
        {{$data["deskripsi"]}}
    </p>
    <div class="flex gap-5">
        @if ($data["url"] != null)
        <a href={{$data["url"]}} class="inline-flex items-center bg-link-blue hover:bg-primary rounded-xl py-3 px-7 text-white text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.36 14c.08-.66.14-1.32.14-2c0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2m-5.15 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 0 1-4.33 3.56M14.34 14H9.66c-.1-.66-.16-1.32-.16-2c0-.68.06-1.35.16-2h4.68c.09.65.16 1.32.16 2c0 .68-.07 1.34-.16 2M12 19.96c-.83-1.2-1.5-2.53-1.91-3.96h3.82c-.41 1.43-1.08 2.76-1.91 3.96M8 8H5.08A7.923 7.923 0 0 1 9.4 4.44C8.8 5.55 8.35 6.75 8 8m-2.92 8H8c.35 1.25.8 2.45 1.4 3.56A8.008 8.008 0 0 1 5.08 16m-.82-2C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2c0 .68.06 1.34.14 2M12 4.03c.83 1.2 1.5 2.54 1.91 3.97h-3.82c.41-1.43 1.08-2.77 1.91-3.97M18.92 8h-2.95a15.65 15.65 0 0 0-1.38-3.56c1.84.63 3.37 1.9 4.33 3.56M12 2C6.47 2 2 6.5 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2Z"/></svg>
            <span class="ml-2">Aplikasi</span>
        </a>
        @endif
        @if ($data["repo_link"] != null)
        <a href={{$data["repo_link"]}} class="inline-flex items-center bg-primary hover:bg-primary-600 rounded-xl py-3 px-7 text-white text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16"><path fill="white" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59c.4.07.55-.17.55-.38c0-.19-.01-.82-.01-1.49c-2.01.37-2.53-.49-2.69-.94c-.09-.23-.48-.94-.82-1.13c-.28-.15-.68-.52-.01-.53c.63-.01 1.08.58 1.23.82c.72 1.21 1.87.87 2.33.66c.07-.52.28-.87.51-1.07c-1.78-.2-3.64-.89-3.64-3.95c0-.87.31-1.59.82-2.15c-.08-.2-.36-1.02.08-2.12c0 0 .67-.21 2.2.82c.64-.18 1.32-.27 2-.27c.68 0 1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82c.44 1.1.16 1.92.08 2.12c.51.56.82 1.27.82 2.15c0 3.07-1.87 3.75-3.65 3.95c.29.25.54.73.54 1.48c0 1.07-.01 1.93-.01 2.2c0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/></svg>
            <span class="ml-2">Source Code</span>
        </a>
        @endif

    </div>
  </div>

</div>
@endsection
