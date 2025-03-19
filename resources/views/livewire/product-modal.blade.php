<div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $productName }}</h5>
        <div class="items-center justify-center  sm:space-x-4 rtl:space-x-reverse">
            <div class="mb-2">
                <livewire:order-manager :product=$productName  />
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                </div>
            </div>
        </div>
    </div>


