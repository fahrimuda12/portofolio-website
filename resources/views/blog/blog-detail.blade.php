@extends('app')
@section('content')
@include('partials.navbar')

<div class="mt-[73px] md:mt-[77px]">
  <div class="jumbotron relative w-full h-80">
    <img src="{{ asset('assets/img/blog.jpg') }}" class="object-cover w-full h-80">
    <div class="absolute w-full h-full top-0 bottom-0 start-0 end-0 flex justify-center items-center bg-black bg-opacity-50">
      <h2 class="text-white font-bold text-[34px] max-md:text-[26px] mb-4 max-md:p-16">How to build a world-class business brand</h2>
    </div>
  </div>

  <div class="mx-[30px] md:mx-[78px]">
    {{-- info blog --}}
    <div class="info mt-[40px] mb-10">
      <p class="text-gray-400">2 min read | 30 Sept 2023</p>
    </div>
    <div class="paragraph">
      <p class="text-hitam-400 mb-6 text-justify text-[14px] md:text-[16px]">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Deserunt temporibus in odit, ut ipsa repudiandae ducimus sapiente, placeat itaque libero eos maiores quia consequatur totam corporis labore non. Voluptatum, voluptatem odio facere quis aut perspiciatis amet optio fugiat! Accusantium, saepe.
      </p>
      <p class="text-hitam-400 mb-6 text-justify text-[14px] md:text-[16px]">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Deserunt temporibus in odit, ut ipsa repudiandae ducimus sapiente, placeat itaque libero eos maiores quia consequatur totam corporis labore non. Voluptatum, voluptatem odio facere quis aut perspiciatis amet optio fugiat! Accusantium, saepe.
      </p>
    </div>
  </div>
</div>
@endsection