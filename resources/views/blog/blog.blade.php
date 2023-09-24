@extends('app')
@section('content')
@include('partials.navbar')

<div class="bg-gradient-to-b from-bg-blue min-h-[400px] mt-[73px] md:mt-[77px]">
  <div class="pt-[40px] md:pt-[80px] mx-[30px] md:mx-[78px]">
    <div class="project ">
      <h2 class="text-hitam font-bold text-[34px] mb-4">My Recent Blogs</h2>
      <p class="text-hitam-400">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ea et voluptatum voluptatibus suscipit cum dignissimos accusantium sequi distinctio, fugit voluptates.</p>

      <div class="card-list mt-[40px]">
        {{-- card --}}
        <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] md:px-14 py-4 md:py-8 mb-6">
          <div class="card-img">
            <img src="{{ asset('assets/img/blog.jpg') }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
          </div>
          <div class="card-txt  mt-4 md:mt-0 md:max-w-[500px]">
            <h3 class="text-hitam font-semibold text-[24px] mb-3">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 mb-6 text-[12px] md:text-[16px] text-justify">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Veniam natus, fugit hic facilis, aspernatur sunt sint, aut quos ullam ab vitae possimus excepturi voluptas modi omnis doloribus quaerat debitis iure tenetur nesciunt!</p>
            <a href="{{ url('/blog/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">Read Now</a>
          </div>
        </div>
        {{-- card --}}
        <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] md:px-14 py-4 md:py-8 mb-6">
          <div class="card-img">
            <img src="{{ asset('assets/img/blog.jpg') }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
          </div>
          <div class="card-txt  mt-4 md:mt-0 md:max-w-[500px]">
            <h3 class="text-hitam font-semibold text-[24px] mb-3">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 mb-6 text-[12px] md:text-[16px] text-justify">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Veniam natus, fugit hic facilis, aspernatur sunt sint, aut quos ullam ab vitae possimus excepturi voluptas modi omnis doloribus quaerat debitis iure tenetur nesciunt!</p>
            <a href="{{ url('/blog/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">Read Now</a>
          </div>
        </div>
        {{-- card --}}
        <div class="card flex flex-col lg:flex-row justify-between items-center min-h-[300px] md:px-14 py-4 md:py-8 mb-6">
          <div class="card-img">
            <img src="{{ asset('assets/img/blog.jpg') }}" class="min-w-[240px] md:max-w-[500px] rounded-xl">
          </div>
          <div class="card-txt  mt-4 md:mt-0 md:max-w-[500px]">
            <h3 class="text-hitam font-semibold text-[24px] mb-3">Ep 1: How to build a world-class business brand</h3>
            <p class="text-hitam-400 mb-6 text-[12px] md:text-[16px] text-justify">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Veniam natus, fugit hic facilis, aspernatur sunt sint, aut quos ullam ab vitae possimus excepturi voluptas modi omnis doloribus quaerat debitis iure tenetur nesciunt!</p>
            <a href="{{ url('/blog/detail') }}" class="text-link-blue font-semibold text-[12px] md:text-[14px]">Read Now</a>
          </div>
        </div>

        

        
      </div>
    </div>
  </div>
</div>
@endsection