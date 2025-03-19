
<div class="bg-gray-100 p-4 sm:p-8 md:p-16 mt-20">
    <div class="container mx-auto">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">



            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" type="button">
                <a class="relative flex h-full flex-col rounded-md border border-gray-200 bg-white p-2.5 hover:border-gray-400 sm:rounded-lg sm:p-5">
        <span class="text-md mb-0 font-semibold text-gray-900 hover:text-black sm:mb-1.5 sm:text-xl">
          Frontend Performance
        </span>
                    <span class="text-sm leading-normal text-gray-400 sm:block">
          Detailed list of best practices to improve your frontend performance
        </span>
                </a>
            </button>




            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" type="button">


                <a
                    class="relative flex h-full flex-col rounded-md border border-gray-200 bg-white p-2.5 hover:border-gray-400 sm:rounded-lg sm:p-5">
        <span class="text-md mb-0 font-semibold text-gray-900 hover:text-black sm:mb-1.5 sm:text-xl">
          Frontend Performance
        </span>
                    <span class="text-sm leading-normal text-gray-400 sm:block">
          Detailed list of best practices to improve your frontend performance
        </span>
                </a>
            </button>







            <a href="/api-security"
               class="relative flex h-full flex-col rounded-md border border-gray-200 bg-white p-2.5 hover:border-gray-400 sm:rounded-lg sm:p-5">
        <span class="text-md mb-0 font-semibold text-gray-900 hover:text-black sm:mb-1.5 sm:text-xl">
          API Security
        </span>
                <span class="text-sm leading-normal text-gray-400 sm:block">
          Detailed list of best practices to make your APIs secure
        </span>
            </a>
            <a href="/code-review"
               class="relative flex h-full flex-col rounded-md border border-gray-200 bg-white p-2.5 hover:border-gray-400 sm:rounded-lg sm:p-5">
        <span class="text-md mb-0 font-semibold text-gray-900 hover:text-black sm:mb-1.5 sm:text-xl">
          Code Reviews
        </span>
                <span class="text-sm leading-normal text-gray-400 sm:block">
          Detailed list of best practices for effective code reviews and quality
        </span>
            </a>
            <a href="/aws"
               class="relative flex h-full flex-col rounded-md border border-gray-200 bg-white p-2.5 hover:border-gray-400 sm:rounded-lg sm:p-5">
        <span class="text-md mb-0 font-semibold text-gray-900 hover:text-black sm:mb-1.5 sm:text-xl">
          AWS
        </span>
                <span class="text-sm leading-normal text-gray-400 sm:block">
          Detailed list of best practices for Amazon Web Services (AWS)
        </span>
            </a>
        </div>
    </div>
    <livewire:total-sum-manager />
</div>




<div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div      class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Ceburekas su kiauliena
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form class="p-4 md:p-5">

                <div class="col-span-2 sm:col-span-1">
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priedai</label>
                </div>

                <div class="flex items-center">
                    <input id="apple" type="checkbox" value=""
                           class="w-4 h-12 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />

                    <label for="apple" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Apple (56)
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="fitbit" type="checkbox" value=""
                           class="w-4 h-12 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />

                    <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Fitbit (56)
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="dell" type="checkbox" value=""
                           class="w-4 h-12 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />

                    <label for="dell" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Dell (56)
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="asus" type="checkbox" value="" checked
                           class="w-40 h-12 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />

                    <label for="asus" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Asus (97)
                    </label>
                </div>

                222
            </form>
            <livewire:order-manager />
        </div>
    </div>
</div>



<x-layouts.app title="Dashboard">
    iuhiuh
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        iuhoiuh


        <!-- Modal toggle -->


        <!-- Main modal -->

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">

                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
<section class=" w-full h-[700px] relative bg-white dark:bg-[#0A2025] ">
    <div class="bg-white flex flex-col h-full absolute right-0 p-10 w-[450px]">
        <div class="flex items-center mb-3 justify-between ">
            <h2 class="text-[#191919] text-xl font-medium leading-[30px]">Shopping Card (2)</h2><svg width="45" height="45"
                                                                                                     viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="22.5" cy="22.5" r="22.5" fill="white"></circle>
                <path d="M28.75 16.25L16.25 28.75" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round"></path>
                <path d="M16.25 16.25L28.75 28.75" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round"></path>
            </svg>
        </div>
        <div class="flex gap-2 mb-6 items-center"><img width="120" height="100" src="https://iili.io/3FqLBsI.png" alt="">
            <div>
                <h3 class="w-[216px]   text-[#191919] text-sm font-normal  leading-[21px]">Fresh Indian Orange</h3>
                <p>
                    <span class="relative justify-start text-[#7f7f7f] text-sm font-normal  leading-[21px]">1 kg x</span><span class="relative justify-start text-[#191919] text-sm font-semibold  leading-[16.80px]"> 12.00</span>
                </p>
            </div><svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_629_6652)">
                    <path
                        d="M12 23C18.0748 23 23 18.0748 23 12C23 5.92525 18.0748 1 12 1C5.92525 1 1 5.92525 1 12C1 18.0748 5.92525 23 12 23Z"
                        stroke="#CCCCCC" stroke-miterlimit="10"></path>
                    <path d="M16 8L8 16" stroke="#666666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path d="M16 16L8 8" stroke="#666666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </g>
                <defs>
                    <clipPath id="clip0_629_6652">
                        <rect width="24" height="24" fill="white"></rect>
                    </clipPath>
                </defs>
            </svg>
        </div>
        <div class="flex  gap-2 mb-6 items-center"><img width="120" height="100" src="https://iili.io/3FqLBsI.png" alt="">
            <div>
                <h3 class="w-[216px]   text-[#191919] text-sm font-normal  leading-[21px]">Fresh Indian Orange</h3>
                <p>
                    <span class="relative justify-start text-[#7f7f7f] text-sm font-normal  leading-[21px]">1 kg x</span><span class="relative justify-start text-[#191919] text-sm font-semibold  leading-[16.80px]"> 12.00</span>
                </p>
            </div><svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_629_6652)">
                    <path
                        d="M12 23C18.0748 23 23 18.0748 23 12C23 5.92525 18.0748 1 12 1C5.92525 1 1 5.92525 1 12C1 18.0748 5.92525 23 12 23Z"
                        stroke="#CCCCCC" stroke-miterlimit="10"></path>
                    <path d="M16 8L8 16" stroke="#666666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path d="M16 16L8 8" stroke="#666666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </g>
                <defs>
                    <clipPath id="clip0_629_6652">
                        <rect width="24" height="24" fill="white"></rect>
                    </clipPath>
                </defs>
            </svg>
        </div>
        <div class="mt-auto">
            <div class=" py-6 bg-white flex justify-between items-center">
                <span class="relative justify-start text-[#191919] text-base font-normal  leading-normal">2 Product</span><span class="relative justify-start text-[#191919] text-base font-semibold  leading-tight">$26.00</span>
            </div>
            <button class="w-full px-10 py-4 bg-[#00b206] rounded-[43px]     text-white text-base font-semibold  leading-tight">Checkout</button><button class="w-[376px] mt-3 px-10 py-4 bg-[#56ac59]/10 rounded-[43px]   text-[#00b206] text-base font-semibold  leading-tight">Go To Cart</button>
        </div>
    </div>
</section>
