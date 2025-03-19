<form wire:submit.prevent="addOrder">
    <div class="p-4 md:p-8">
        <h3 class="mb-5 text-lg font-medium text-gray-900 dark:text-white">Pasirinkti priedus:</h3>
        <ul class="grid w-full gap-6 md:grid-cols-3">
            @foreach($allToppings as $topping)
                <li>
                    <input type="checkbox" id="{{$topping->name}}" value="{{$topping->name}}" wire:model="toppings"
                           class="hidden peer">
                    <label for="{{$topping->name}}"
                           class="inline-flex items-center justify-between w-full p-15 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 dark:peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <div class="flex-1  text-2xl">
                            {{$topping->name}}
                        </div>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>
    <button type="submit" data-modal-toggle="crud-modal"
            class="rounded-lg cursor-pointer flex-1 bg-gray-500 w-100 h-25 text-white inline-flex items-center bg-blue-700 hover:bg-[#00b206] focus:ring-4 focus:outline-none focus:ring-blue-300 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

        <div class="flex-1  text-2xl">
            Prideti
        </div>
    </button>
    <br><br>
</form>
