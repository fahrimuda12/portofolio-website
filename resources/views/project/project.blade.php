@extends('app')
@section('content')
@include('partials.navbar')

<div class="bg-gradient-to-b from-bg-blue min-h-[100vh] mt-[73px] md:mt-[77px]">
  <div class="pt-[40px] md:pt-[80px] mx-[30px] md:mx-[78px]">
    {{-- project wrapper --}}
    <div class="project ">
      <h2 class="text-hitam font-bold text-[34px] mb-4">My Projects</h2>
      <p class="text-hitam-400">Ini adalah beberapa projek yang telah saya kerjakan</p>

      <div class="card-list mt-[40px]">
        @foreach ($data as $value)
             {{-- card --}}
            <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] rounded-2xl bg-white px-9 md:px-14 py-4 md:py-8 mb-6">
                <div class="card-txt order-2 lg:order-1 mt-4 md:mt-0 md:max-w-[500px]">
                <h3 class="text-hitam font-semibold text-[24px] mb-3">{{$value["judul"]}}</h3>
                <p class="text-hitam-400 mb-6 text-[12px] md:text-[16px] text-justify">{{$value["deskripsi"]}}</p>
                <a href="{{ url('/project/'.$value["project_id"].'/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">View Case Study</a>
                </div>
                <div class="card-img order-1 lg:order-2">
                <img src="{{ $value->thumbnail_link }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
                </div>
            </div>
        @endforeach
      </div>

      {{-- pagination --}}
      {{-- <div class="flex mt-5 mb-10 justify-end">
        <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px text-sm">
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
            </li>
            <li>
                <a href="#" aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">4</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">5</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
            </li>
            </ul>
        </nav>
      </div> --}}
    </div>
  </div>
</div>
@endsection
