@extends('app')
@section('content')
@include('partials.navbar')
  {{-- bg hero --}}
  <div class="hero-bg bg-gradient-to-b from-bg-blue min-h-[400px] mt-[73px] md:mt-[77px]">
    {{-- hero wrapper --}}
    <div class="pt-[40px] md:pt-[80px] mx-[30px] md:mx-[78px] hero-wrapper flex flex-col lg:flex-row items-center justify-around ">
      <div class="hero-txt order-2 mt-12 md:mt-0 lg:order-1">
        <span class="flex text-[26px] sm:max-md:text-[30px] md:text-[34px] text-primary font-bold">
          <img src="{{ asset('assets/img/waving-hand.svg') }}" class="w-9 mr-3">
          Hello!
        </span>
        <h1 class="text-hitam text-[36px] sm:max-md:text-[40px] md:text-[44px] font-bold mb-9">I'm Fahri Muda, a Web and Backend Developer</h1>
        <div class="flex">
          <a href="#" class="bg-primary hover:bg-primary-600 rounded-xl py-3 px-7 text-white text-base">
            <div class="flex items-center ">
              <span class="mr-2">About Me</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="white" fill-rule="evenodd" d="M3.464 20.535C4.93 22 7.286 22 12 22c4.714 0 7.071 0 8.535-1.465C22 19.072 22 16.714 22 12s0-7.071-1.465-8.536C19.072 2 16.714 2 12 2S4.929 2 3.464 3.464C2 4.93 2 7.286 2 12c0 4.714 0 7.071 1.464 8.535ZM7.25 12a.75.75 0 0 1 .75-.75h6.19l-1.72-1.72a.75.75 0 0 1 1.06-1.06l3 3a.75.75 0 0 1 0 1.06l-3 3a.75.75 0 1 1-1.06-1.06l1.72-1.72H8a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
              </svg>
            </div>
          </a>
        </div>
      </div>
      <div class="hero-img order-1 lg:order-2">
        <img src="{{ asset('assets/img/img-hero.png') }}" class="min-w-[260px] md:max-w-[520px]">
      </div>
    </div>
  </div>

  <div class="mx-[30px] md:mx-[78px]">
    {{-- project wrapper --}}
    <div class="project mt-[100px]">
      <h2 class="text-hitam font-bold text-[34px] mb-4">My Recent Projects</h2>
      <a href="/project" class="text-hitam-400 text-[20px] font-semibold hover:text-hitam group">
        <div class="flex items-center">
          <span class="mr-3">See All</span>
          <svg xmlns="http://www.w3.org/2000/svg"  width="24" height="24" viewBox="0 0 24 24"><g fill="none">
            <path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/>
            <path class="fill-hitam group-hover:fill-hitam-400" d="m14.707 5.636l5.657 5.657a1 1 0 0 1 0 1.414l-5.657 5.657a1 1 0 0 1-1.414-1.414l3.95-3.95H4a1 1 0 1 1 0-2h13.243l-3.95-3.95a1 1 0 1 1 1.414-1.414Z"/>
          </g></svg>
        </div>
      </a>

      <div class="card-list mt-[40px]">
        @foreach ($project["data"] as $value)
             {{-- card --}}
            <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] rounded-2xl bg-neutral px-9 md:px-14 py-4 md:py-8 mb-6">
                <div class="card-txt order-2 lg:order-1 mt-4 md:mt-0 md:max-w-[500px]">
                <h3 class="text-hitam font-semibold text-[24px] mb-3">{{$value->judul}}</h3>
                <p class="text-hitam-400 mb-6 text-[12px] md:text-[16px] text-justify">{{$value->deskripsi}}</p>
                <a href="{{ url('/project/'.$value["project_id"].'/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">View Case Study</a>
                </div>
                <div class="card-img order-1 lg:order-2">
                <img src="{{ $value->thumbnail_link }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
                </div>
            </div>
        @endforeach


        {{-- card --}}
        {{-- <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] rounded-2xl bg-neutral px-9 md:px-14 py-4 md:py-8 mb-6">
          <div class="card-txt order-2 lg:order-1 mt-4 md:mt-0 md:max-w-[500px]">
            <h3 class="text-hitam font-semibold text-[24px] mb-3">Tenderplus.id</h3>
            <p class="text-hitam-400 mb-6 text-justify text-[12px] md:text-[16px]">Proyek saat magang MSIB dari Kampus Merdeka di PT.Baracipta Esa Engineering. Dalam proyek ini, saya mempunyai tugas untuk menangani Aplikasi Backend dan membuatnya
                otomatisasi untuk menghapus data dari situs web lain.</p>
            <a href="{{ url('/project/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">View Case Study</a>
          </div>
          <div class="card-img order-1 lg:order-2">
            <img src="{{ asset('assets/img/proyek/tender-landing.png') }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
          </div>
        </div> --}}
      </div>

    </div>

    {{-- skills wrapper --}}
    <div class="skill-wrapper mt-[120px]">
      <h2 class="text-hitam font-bold text-[26px] sm:max-lg:text-[30px] lg:text-[34px]">My Various Skills</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 place-content-center place-items-center mt-[40px]">
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/php-1.svg') }}" class="w-[80px]">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">PHP</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/laravel-2.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Laravel</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
            <img src="{{ asset('assets/img/lumen-1.png') }}" class="w-10 h-14">
            <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Lumen</span>
          </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/html-1.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">HTML</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/css-3.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">CSS</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/logo-javascript.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Javascript</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
           <img src="{{ asset('assets/img/tailwind-css-2.svg') }}" class="w-14">
           <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Tailwind</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/bootstrap-5-1.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Bootstrap</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/react-2.svg') }}" class="w-14">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">React</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
          <img src="{{ asset('assets/img/flutter-logo.svg') }}" class="w-12">
          <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Flutter</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
            <img src="{{ asset('assets/img/express-1.png') }}" class="w-14">
            <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Express JS</span>
        </div>
        {{-- skill --}}
        <div class="skill flex flex-col justify-center items-center mb-4">
            <img src="{{ asset('assets/img/selenium-1.png') }}" class="w-12">
            <span class="mt-3 text-hitam-400 text-sm font-semibold text-center">Selenium</span>
        </div>
      </div>
    </div>

    {{-- blogs wrapper --}}
    {{-- <div class="blog-wrapper mt-[120px]">
      <div class="blog-title mb-[40px]">
        <h2 class="text-hitam font-bold text-[26px] sm:max-lg:text-[30px] lg:text-[34px]">My Recent Blogs</h2>
        <p class="text-hitam-400 text-[14px] md:text-[16px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis eaque tempora omnis. Dolor, rem similique.</p>
      </div>

      <div class="blog-card flex flex-col lg:flex-row justify-between items-center xl:gap-8">
        card
        <div class="card flex flex-col justify-center items-center min-w-[300px] lg:max-w-[360px] p-4">
          <div class="card-img bg-cover mb-6">
            <img class="min-w-[200px] min-h-[120px] lg:max-h-[240px] xl:max-w-[360px] rounded-xl object-cover" src="{{ asset('assets/img/blog.jpg') }}">
          </div>
          <div class="card-txt">
            <h3 class="text-hitam font-semibold text-[20px] mb-4">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 text-justify mb-4 text-[14px] md:text-[16px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam nulla iure at?</p>
            <a href="#" class="text-[12px] md:text-[14px] text-link-blue hover:text-blue-900 font-semibold">Read Now</a>
          </div>
        </div>
        card
        <div class="card flex flex-col justify-center items-center min-w-[300px] lg:max-w-[360px] p-4">
          <div class="card-img bg-cover mb-6">
            <img class="min-w-[200px] xl:max-w-[360px] rounded-xl" src="{{ asset('assets/img/blog.jpg') }}">
          </div>
          <div class="card-txt">
            <h3 class="text-hitam font-semibold text-[20px] mb-4">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 text-justify mb-4 text-[14px] md:text-[16px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam nulla iure at?</p>
            <a href="#" class="text-[12px] md:text-[14px] text-link-blue hover:text-blue-900 font-semibold">Read Now</a>
          </div>
        </div>
        card
        <div class="card flex flex-col justify-center items-center min-w-[300px] lg:max-w-[360px] p-4">
          <div class="card-img bg-cover mb-6">
            <img class="min-w-[200px] xl:max-w-[360px] rounded-xl" src="{{ asset('assets/img/blog.jpg') }}">
          </div>
          <div class="card-txt">
            <h3 class="text-hitam font-semibold text-[20px] mb-4">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 text-justify mb-4 text-[14px] md:text-[16px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam nulla iure at?</p>
            <a href="#" class="text-[12px] md:text-[14px] text-link-blue hover:text-blue-900 font-semibold">Read Now</a>
          </div>
        </div>
      </div>
    </div> --}}

    {{-- subscribe wrapper --}}
    <div class="subscribe-wrapper flex flex-col justify-center items-center mt-[120px]">
      <div class="img-mail bg-neutral rounded-full min-w-[100px] max-w-[160px] p-5 mb-7">
        <img src="{{ asset('assets/img/mailbox.svg') }}" class="min-w-[100px] max-w-[160px]">
      </div>
      <div class="mail-title text-center mb-8">
        <h1 class="text-hitam text-[34px] sm:max-md:text-[36px] md:text-[44px] font-bold mb-3">Subscribe For the Latest Updates</h1>
        <p class="text-hitam-400 text-[16px] sm:max-md:text-[20px] md:text-[22px]">Subscribe to newsletter and never miss the new post every week.</p>
      </div>
      <div class="mail-input min-w-[300px] md:min-w-[600px] flex">
        <input type="email" id="email" class="mr-4 bg-gray-50 border border-gray-300 text-hitam text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter your email here..." autocomplete="off">
        <button class="bg-primary hover:bg-primary-600 rounded-xl py-3 px-6 text-white text-base">
          <span class="hidden md:block">Subscribe</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 28 28" class="block md:hidden">
            <path fill="white" d="M3.79 2.625c-.963-.46-2.021.42-1.746 1.451l2.016 7.533a1 1 0 0 0 .824.732l9.884 1.412c.286.04.286.454 0 .495l-9.883 1.411a1 1 0 0 0-.824.732l-2.017 7.537c-.275 1.03.783 1.91 1.746 1.451L25.288 15.13c.949-.452.949-1.804 0-2.257L3.79 2.626Z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- contact wrapper --}}
    <div class="mt-[120px] w-full bg-gradient-to-l from-bg-blue">
      <div class="flex flex-col md:flex-row justify-center items-center mx-[10px]">
        <img src="{{ asset('assets/img/contact-me.png') }}" class="max-w-[160px] md:max-w-[340px] top-12 max-sm:hidden">
        <div class="txt max-sm:text-center max-sm:my-[60px] lg:w-[650px]">
          <h2 class="text-hitam font-bold text-[30px] sm:max-lg:text-[24px] lg:text-[34px] mb-[38px]">Let's work together and make everything super useful.</h2>
          <a href="#" class="bg-primary hover:bg-primary-600 rounded-xl py-3 px-4 text-white text-[12px] md:text-[16px] mt-4">
            Contact Me
          </a>
        </div>
      </div>
    </div>

    {{-- footer --}}
    <div class="w-full bg-primary py-2">
      <p class="text-xs text-center text-white mb-2">&copy; Copyright 2023</p>
      {{-- <p class="text-xs text-center text-white">
        All rights reserved | 3D Illustration by
        <a href="https://icons8.com" class="font-bold underline">Icons8</a>
      </p> --}}
    </div>
@endsection
