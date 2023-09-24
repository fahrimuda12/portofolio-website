<nav class="bg-white fixed w-full z-20 top-0 left-0 border-b border-gray-200 shadow-sm">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-[30px] md:mx-[66px] py-4">
    <a href="{{ url('/') }}" class="flex items-center">
        <span class="self-center text-md md:text-2xl font-semibold whitespace-nowrap text-[#001858]">fahrimuda</span>
    </a>
    <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
      <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
    </button>
      <div class="md:flex items-center justify-between hidden w-full md:w-auto" id="navbar-sticky">
        <ul class="flex md:items-center flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
          <li>
            <a href="{{ url('/project') }}" class="block py-2 pl-3 pr-4 text-gray-900 bg-blue-700 rounded md:bg-transparent md:hover:text-blue-700 md:p-0" aria-current="page">Projects</a>
          </li>
          <li>
            <a href="{{ url('/blog') }}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Blogs</a>
          </li>
          <li>
            <a href="{{ url('/about') }}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">About Me</a>
          </li>
          <li>
            <div class="button md:bg-primary md:hover:bg-primary-600 md:rounded-xl md:py-[10px] md:px-[14px]">
              <a href="{{ url('/contact') }}" class="block py-2 pl-3 pr-4 text-white rounded hover:bg-gray-100 md:hover:bg-transparent md:p-0">Contact Me</a>

            </div>
          </li>
        </ul>
      </div>
  </div>
</nav>