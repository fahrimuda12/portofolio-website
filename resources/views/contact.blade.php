@extends('app')
@section('content')
@include('partials.navbar')

<div class="bg-gradient-to-b from-bg-blue min-h-[100vh] mt-[73px] md:mt-[77px]">
  <div class="pt-[40px] md:pt-[80px] mx-[30px] md:mx-[80px] lg:grid lg:grid-cols-2 lg:place-items-center">
    <div class="socmed">
      {{-- card socmed --}}
      <a href="https://instagram.com/fahrimuda" class="group">
        <div class="card-socmed bg-white rounded-xl min-h-[50px] lg:w-[400px] flex justify-start items-center p-6 group-hover:bg-gray-50 mb-4">
          <div class="socmed-icon rounded-full h-14 w-14 flex justify-center items-center bg-gray-100 mr-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="fill-primary">
              <path d="M8 0C5.829 0 5.556.01 4.703.048C3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7C.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297c.04.852.174 1.433.372 1.942c.205.526.478.972.923 1.417c.444.445.89.719 1.416.923c.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417c.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046c.78.035 1.204.166 1.486.275c.373.145.64.319.92.599c.28.28.453.546.598.92c.11.281.24.705.275 1.485c.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598c-.28.11-.704.24-1.485.276c-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598a2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485c-.038-.843-.046-1.096-.046-3.233c0-2.136.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486c.145-.373.319-.64.599-.92c.28-.28.546-.453.92-.598c.282-.11.705-.24 1.485-.276c.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92a.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217a4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334a2.667 2.667 0 0 1 0-5.334z"/>
            </svg>
          </div>
          <div class="socmed-info">
            <h5 class="text-hitam text-[20px] font-semibold mb-2">Instagram</h5>
            <p class="text-hitam-400 text-[14px]">@fahrimuda</p>
          </div>
        </div>
      </a>
      {{-- card socmed --}}
      <a href="https://wa.me/6282165787290" class="group">
        <div class="card-socmed bg-white rounded-xl min-h-[50px] lg:w-[400px] flex justify-start items-center p-6 group-hover:bg-gray-50 mb-4">
          <div class="socmed-icon rounded-full h-14 w-14 flex justify-center items-center bg-gray-100 mr-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="fill-primary">
              <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144l-2.494.654l.666-2.433l-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931a6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646c-.182-.065-.315-.099-.445.099c-.133.197-.513.646-.627.775c-.114.133-.232.148-.43.05c-.197-.1-.836-.308-1.592-.985c-.59-.525-.985-1.175-1.103-1.372c-.114-.198-.011-.304.088-.403c.087-.088.197-.232.296-.346c.1-.114.133-.198.198-.33c.065-.134.034-.248-.015-.347c-.05-.099-.445-1.076-.612-1.47c-.16-.389-.323-.335-.445-.34c-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654c0 .977.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992c.47.205.84.326 1.129.418c.475.152.904.129 1.246.08c.38-.058 1.171-.48 1.338-.943c.164-.464.164-.86.114-.943c-.049-.084-.182-.133-.38-.232z"/>
            </svg>
          </div>
          <div class="socmed-info">
            <h5 class="text-hitam text-[20px] font-semibold mb-2">WhatsApp</h5>
            <p class="text-hitam-400 text-[14px]">+62821-6578-7290</p>
          </div>
        </div>
      </a>
      {{-- card socmed --}}
      <a href="https://instagram.com/fahrimuda" class="group">
        <div class="card-socmed bg-white rounded-xl min-h-[50px] lg:w-[400px] flex justify-start items-center p-6 group-hover:bg-gray-50 mb-4">
          <div class="socmed-icon rounded-full h-14 w-14 flex justify-center items-center bg-gray-100 mr-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-primary">
              <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6zm3.519 0L12 11.671L18.481 6H5.52zM20 7.329l-7.341 6.424a1 1 0 0 1-1.318 0L4 7.329V18h16V7.329z"/>
            </svg>
          </div>
          <div class="socmed-info">
            <h5 class="text-hitam text-[20px] font-semibold mb-2">Email Address</h5>
            <p class="text-hitam-400 text-[14px]">fahrimuda12@gmail.com</p>
          </div>
        </div>
      </a>
    </div>

    <div class="contact-input bg-white rounded-xl p-6 min-h-full min-w-full">
      <h3 class="text-hitam font-semibold text-[24px] mb-2">Contact Me</h3>
      <p class="text-hitam-400 text-[14px] mb-6">Interested in collaborating? Please fill the form below.</p>
      <div class="contact-form mb-4">
        <div class="input-group md:flex mb-3 md:justify-between">
          <input type="text" name="" id="" class="mb-4 md:mb-0 w-full mr-2 rounded-lg bg-neutral border-1 border-gray-300" placeholder="Your name" autocomplete="off">
          <input type="email" name="" id="" class="w-full mr-2 rounded-lg bg-neutral border-1 border-gray-300" placeholder="email@email.com" autocomplete="off">
        </div>
        <textarea name="message" id="" rows="3" class="w-full rounded-lg border-1 border-gray-300"></textarea>
      </div>
      <button type="submit" class="text-white bg-blue-500 dark:bg-blue-600 font-medium rounded-lg text-sm px-8 py-2.5 text-center">Send</button>
  
    </div>
  </div>

  
</div>

@endsection